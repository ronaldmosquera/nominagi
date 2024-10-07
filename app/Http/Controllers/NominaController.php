<?php

namespace App\Http\Controllers;

use App\Models\AlcanceNomina;
use App\Models\Cargo;
use App\Models\NominasPasadas;
use App\Models\PartyIdentification;
use Illuminate\Http\Request;
use App\Models\Nomina;
use App\Models\ForeginContrataciones;
use Carbon\Carbon;
use App\Models\Contrataciones;
use App\Models\Person;
use App\Models\ImagenesRoles;
use App\Models\FinalizacionContratacion;
use App\Models\ConfiguracionVariablesEmpresa;
use App\Models\DetalleContratacion;
use App\Models\EftAccount;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PagoDecimos;
use App\Models\Party;
use App\Models\PartyAcctgPreference;
use App\Models\PartyAutorizacionSri;
use App\Models\Payment;
use App\Models\PaymentApplication;
use App\Models\PaymentMethod;
use App\Models\ProductStore;
use App\Models\ReferenciaPago;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NominaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $montoPagoFactura = 0;

    public function index(Request $request)
    {
        ini_set('max_execution_time', 120);

        if($request->store == 1){
            $date = Carbon::now()->subMonth(1);
            $diaMes = diasMes(intval($date->format('m')));

            $existNomina = Nomina::whereBetween('fecha_nomina',[$date->format('Y-m-01'),$date->format('Y-m-'.$diaMes)])->count();
            if($existNomina > 0)
                return view('layouts.views.nomina.rol_empleado',[
                'var'=> true,
                 'message' => 'La nómina perteneciente a este mes ya está realizada puede ver los roles de pagos generados haciendo clic en el siguiente enlace: <i class="fa fa-file-pdf-o" aria-hidden="true"></i> <a href="'.route('vista.roles-pago',['estado'=>0]).'"> Roles de pago</a>'
            ]);

        }

        //dd(Carbon::now()->subDay(Carbon::now()->format('d'))->format('Y-m-d'));
        $existNomina = Nomina::whereBetween('fecha_nomina',[Carbon::now()->subDay(Carbon::now()->format('d'))->format('Y-m-01'),Carbon::now()->subDay(Carbon::now()->format('d'))->format('Y-m-d')])->count();
        //dd($existNomina);
        if($existNomina>0){
            $dataNomina = Nomina::get();

            $groupNomina = [];
            $data = [];
            foreach ($dataNomina as $dN){
                $groupNomina[Carbon::parse($dN->fecha_nomina)->format('Y-m-d')][] = $dN;
            }

            foreach ($groupNomina as $gN){
                foreach($gN as $n) {
                    $dataEmpleado = Person::where('person.party_id', $n->id_empleado)
                        ->join('party_identification as pi', 'person.party_id', 'pi.party_id')
                        ->join('party_identification_type as pit', 'pi.party_identification_type_id', 'pit.party_identification_type_id')
                        ->select('person.first_name', 'person.last_name', 'pit.description', 'pi.id_value', 'person.party_id')->first();

                    $data[] = [
                        "nombre"=>$dataEmpleado->first_name." ".$dataEmpleado->last_name,
                        "cargo" => isset(getCargo($n->id_empleado)->nombre) ? getCargo($n->id_empleado)->nombre: "Liquidado",
                        "identificacion" => $dataEmpleado->id_value,
                        "total" => $n->total,
                        "nombre_imagen" => getImagenRolPago($n->id_empleado,$n->fecha_nomina)->nombre_imagen,
                        'fecha_nomina' => $n->fecha_nomina,
                    ];
                }
            }

            foreach ($data as $d){
                $groupData[Carbon::parse($d['fecha_nomina'])->format('Y-m-d')][] =  $d;
            }

            arsort($groupData);
            return view('layouts.views.nomina.reporte_nomina',[
                'dataGeneral' => manualPagination($groupData,10)
            ]);


        }

        $dataContrataciones = Contrataciones::join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')
            ->join('tipo_contrato as tc', 'contrataciones.id_tipo_contrato','tc.id_tipo_contrato')
            ->where([
                ['contrataciones.id_tipo_contrato_descripcion',2],
                ['contrataciones.estado',1],
                ['dc.fecha_expedicion_contrato','<=',Carbon::now()->subDays(Carbon::now()->format('d'))]
            ]);

       // dd($dataContrataciones->get());
        if(isset($request->id_empleado) && $request->store == 2)
            $dataContrataciones->where('contrataciones.id_empleado',$request->id_empleado);

        $dataContrataciones = $dataContrataciones->get();

        if(count($dataContrataciones) < 1){
            return view('layouts.views.nomina.rol_empleado',[
                'var'=> true,
                'message' => 'No exiten contrataciones activas para realizar una nómina o las contrataciones actuales no cumplen un mes de vigencia aún'
                ]);
        }

        $date      = Carbon::now()->subMonth(1);
        $ultimoDia = getUltimoDiaMes($date->format('Y-m-d'));
        $inicio    = $date->format('Y-m-01');
        $fin       = $date->format('Y-m-'.$ultimoDia);
        $dataVistaRolesGeneral = [];

        foreach ($dataContrataciones as $key => $contrataciones) {

            $salario            = getSalarioEmpleado($contrataciones->id_empleado,$inicio);
            $horasExtras        = getHorasExtras($contrataciones->id_empleado,$request->store,$inicio,$fin);
            $comisiones         = getComisiones($contrataciones->id_empleado,$inicio,$fin);
            $decimoTercero      = 0.00;
            $decimoCuarto       = 0.00;
            $fondoReserva       = 0.00;
            $aportePersonalIESS = 0.00;
            $aportePatronalIESS = 0.00;

            if($contrataciones->relacion_dependencia){

                $fechaActual = Carbon::now()->toDateString();

                //////// DECIMO TERCERO ////////
                if($contrataciones->decimo_tercero){//Mensualizado

                    $decimoTercero = getDecimoTercero($salario,$horasExtras,$contrataciones->id_empleado);

                }else{

                    if($fechaActual >= Carbon::now()->format('Y-12-01') && $fechaActual <= Carbon::now()->format('Y-12-31'))
                        $decimoTercero = getDecimoTerceroAnual($contrataciones->id_empleado);
                }
                //////// FIN DECIMO TERCERO ////////


                /////////// DECIMO CUARTO ////////
                if($contrataciones->decimo_cuarto){//Mensualizado

                    $decimoCuarto  = getDecimoCuarto();

                }else{

                    if($fechaActual >= Carbon::now()->format('Y-08-01') && $fechaActual <= Carbon::now()->format('Y-08-15') )
                        $decimoCuarto = getDecimoCuartoAnual($contrataciones->id_empleado,true);

                }
                ////////// FIN DECIMO CUARTO ////////


                ////////// FONDO DE RESERVA //////////
                if(Carbon::now()->diffInYears($contrataciones->fecha_expedicion_contrato) >= getConfiguracionEmpresa()->anno_calculo_fondo_reserva)
                    if($contrataciones->fondo_reserva){//Mensualizado

                        $fondoReserva = getFondoReserva($salario,$horasExtras);

                    }else{

                        $fondoReserva = getFondoReservaAnual($contrataciones->id_empleado);

                    }
                ////////// FIN FONDO DE RESERVA //////////

                $aportePersonalIESS = getAportePersonal($salario,$horasExtras,$contrataciones->id_contrataciones,$fin);
                $aportePatronalIESS = getAportePatronal($salario,$horasExtras,$contrataciones->id_contrataciones,$fin);

            }

            $arrBonosFijos   = getBonosFijos($contrataciones->id_contrataciones,false,0,$fin);
            $arrPrestamos    = getPrestamos($contrataciones->id_contrataciones,$request->store,false,$fin);
            $arrAnticipos    = getAnticipos($contrataciones->id_empleado,$request->store,false,$fin);
            $consumos        = getConsumos($contrataciones->id_empleado,$request->store,false,$fin);
            $anticipos       = $arrAnticipos['montoAnticipos'];
            $otrosDescuentos = getOtrosDescuentos($contrataciones->id_empleado,$request->store,false,$fin);

            $ingresos = $salario + $horasExtras + $comisiones + $decimoTercero + $decimoCuarto + $fondoReserva + $arrBonosFijos['montoBonosFijos'];
            $egresos  = $aportePersonalIESS + $consumos + $anticipos + $otrosDescuentos['totalOtrosDescuentos'] + $arrPrestamos['montoPrestamos'];

            $subTotal = $ingresos - $egresos;

            $total = $subTotal;
            $iva            = false;
            $retencionIva   = false;
            $retencionRenta = false;

            if(!$contrataciones->relacion_dependencia){
                //$subTotal = $subTotal+$horasExtras+$comisiones;
                $iva            = $subTotal * ($contrataciones->iva/100);
                $retencionIva   = $iva * ($contrataciones->retencion_iva/100);
                $retencionRenta = $subTotal * ($contrataciones->retencion_renta/100);
                $total          = $subTotal + $iva - $retencionIva - $retencionRenta;
            }

            if($request->store == 1){

                $objNomina = new Nomina;
                $objNomina->id_empleado  = $contrataciones->id_empleado;
                $objNomina->fecha_nomina = Carbon::now()->subMonth(1)->format("Y-m-05");
                $objNomina->id_contrataciones = $contrataciones->id_contrataciones;
                $objNomina->total        = $total;

                if($objNomina->save()){

                    if($contrataciones->relacion_dependencia){
                        $model = Nomina::all()->last();
                        $decimoTerceroMensual = getDecimoTercero($salario, $horasExtras, $contrataciones->id_empleado);
                        $vacacionMensual      = number_format(getVacaciones($salario, $horasExtras), 2, ".", "");
                        $fondoReservaMensual  = getFondoReserva($salario, $horasExtras);
                        saveDataMensualRelacionDependecia(
                            $contrataciones,
                            $model,
                            $decimoTerceroMensual,
                            $vacacionMensual,
                            $fondoReservaMensual,
                            $aportePatronalIESS
                        );
                    }
                }
            }

            $dataEmpleado = Person::where('person.party_id',$contrataciones->id_empleado)
                ->join('party_identification as pi','person.party_id','pi.party_id')
                ->join('party_identification_type as pit','pi.party_identification_type_id','pit.party_identification_type_id')
                ->select('person.first_name','person.last_name','pit.description','pi.id_value','person.party_id')->first();

            $dataVistaRolesGeneral[]=[
                'nombre_empleado'      => $dataEmpleado->first_name." ". $dataEmpleado->last_name,
                'cargo'                => Cargo::where('id_cargo',$contrataciones->id_cargo)->first()->nombre,
                'documento'            => $dataEmpleado->description,
                'identificacion'       => $dataEmpleado->id_value,
                'id_empleado'          => $dataEmpleado->party_id,
                'salario'              => $salario,
                'horas_extras'         => $horasExtras,
                'comisiones'           => $comisiones,
                'decimo_tercero'       => $contrataciones->relacion_dependencia == 1 ? $decimoTercero : 'N/A',
                'decimo_cuarto'        => $contrataciones->relacion_dependencia == 1 ? $decimoCuarto : 'N/A',
                'fondo_reserva'        => $contrataciones->relacion_dependencia == 1 ? $fondoReserva : 'N/A',
                'aporte_personal_IESS' => $contrataciones->relacion_dependencia == 1 ? $aportePersonalIESS : 'N/A',
                'aporte_patronal_IEES' => $contrataciones->relacion_dependencia == 1 ? $aportePatronalIESS : 'N/A',
                'consumos'             => $consumos,
                'anticipos'            => $anticipos,
                'otros_descuentos'     => $otrosDescuentos['arrOtrosDescuentos'],
                'arrPrestamos'         => $arrPrestamos['arrPrestamos'],
                'arr_bonos_fijos'      => $arrBonosFijos['arrBonosFijos'],
                'iva'                  => $iva,
                'retencionIva'         => $retencionIva,
                'retencionRenta'       => $retencionRenta,
                'ingresos'             => $ingresos + $iva,
                'Egresos'              => $egresos + $retencionIva + $retencionRenta,
                'total'                => number_format($total,2,".",""),
                'id_contratacion'      => $contrataciones->id_contrataciones
            ];

            if($request->store == 1 || (isset($request->id_empleado) && $request->store == 2)) {
                $dataRolIndividual = [
                    'nombre_empleado'       => $dataEmpleado->first_name . " " . $dataEmpleado->last_name,
                    'cargo'                 => Cargo::where('id_cargo', $contrataciones->id_cargo)->first()->nombre,
                    'documento'             => $dataEmpleado->description,
                    'identificacion'        => $dataEmpleado->id_value,
                    'id_empleado'           => $dataEmpleado->party_id,
                    'salario'               => $salario,
                    'horas_extras'          => $horasExtras,
                    'comisiones'            => $comisiones,
                    'decimo_tercero'        => $contrataciones->relacion_dependencia == 1 ? $decimoTercero : 'N/A',
                    'decimo_cuarto'         => $contrataciones->relacion_dependencia == 1 ? $decimoCuarto : 'N/A',
                    'fondo_reserva'         => $contrataciones->relacion_dependencia == 1 ? $fondoReserva : 'N/A',
                    'aporte_personal_IESS'  => $contrataciones->relacion_dependencia == 1 ? $aportePersonalIESS : 'N/A',
                    'aporte_patronal_IEES'  => $contrataciones->relacion_dependencia == 1 ? $aportePatronalIESS : 'N/A',
                    'consumos'              => $consumos,
                    'anticipos'             => $anticipos,
                    'otros_descuentos'      => $otrosDescuentos['arrOtrosDescuentos'],
                    'arrPrestamos'          => $arrPrestamos['arrPrestamos'],
                    'arr_bonos_fijos'       => $arrBonosFijos['arrBonosFijos'],
                    'iva'                   => $iva,
                    'retencionIva'          => $retencionIva,
                    'retencionRenta'        => $retencionRenta,
                    'ingresos'              => $ingresos + $iva,
                    'egresos'               => $egresos + $retencionIva +$retencionRenta,
                    'consecutivos'          => false,
                    'total'                 => number_format($total,2,".",""),
                    'id_contratacion'       => $contrataciones->id_contrataciones
                ];

                if ($request->store == 1){
                    $nombreRol = 'rol-' . Carbon::now()->subMonth(1)->format('Y-m-05') . '-' . ($key + 1) . '-(' . $dataEmpleado->first_name . "-" . $dataEmpleado->last_name . ').pdf';
                    $objImagenesRoles = new ImagenesRoles;
                    $objImagenesRoles->id_empleado   = $contrataciones->id_empleado;
                    $objImagenesRoles->fecha_nomina  = Carbon::now()->subMonth(1)->format('Y-m-05');
                    $objImagenesRoles->nombre_imagen = $nombreRol;

                    if($objImagenesRoles->save()){

                        $view = \View::make('layouts.views.nomina.recibo_pago_individual_pdf', compact('dataRolIndividual'))->render();
                        $pdf = \App::make('dompdf.wrapper');
                        $pdf->loadHTML($view);
                        $pdf->save(public_path('roles_pago') . '/'.$nombreRol);
                    }

                }else if($request->store == 2){
                    return view('layouts.views.nomina.rol_empleado', [
                        'dataVistaNomina' => $dataRolIndividual
                    ]);
                }
            }
        }

        if($request->store == 0) {
            return view('layouts.views.nomina.list', [
                'dataVistaNomina' => $dataVistaRolesGeneral
            ]);
        }
        else if($request->store == 1){
            $view =  \View::make('layouts.views.nomina.recibo_pago_general_pdf',compact('dataVistaRolesGeneral'))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $number = mt_rand();
            $pdf->save(public_path('roles_pago').'/-rol'.$number.'('.$dataEmpleado->first_name." ". $dataEmpleado->last_name.').pdf');
            return $pdf->stream('recibo_pago');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('layouts.views.nomina.partials.form_upload_roles');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $msg ='';
        $status = 0;

        foreach ($request->file as $image){

            $validaImagen = Validator::make($request->file, [
                'mimeType' => 'image',
            ]);

            if (!$validaImagen->fails()) {

                $nombreImagen = mt_rand() . '_' . mt_rand() . $image->getClientOriginalName();

                $arr = explode(".",$image->getClientOriginalName());

                $identificacionEmpleado = $arr[0];

                $idEmpleado = PartyIdentification::where('id_value',$identificacionEmpleado)->select('party_id')->first();

                if(!isset($idEmpleado->party_id) && empty($idEmpleado->party_id)){
                    $msg .= '<div class="alert alert-danger" role="alert" style="margin: 10px">
                                No se ha guardado la imagen con la identificación '.$identificacionEmpleado.' ya que no existe, corríjala e intente nuavamente con esta imagen
                            </div>';
                    $status = 0;
                }

                if(isset($idEmpleado->party_id) && !empty($idEmpleado->party_id)) {

                    Storage::disk('imagenes_roles')->put($nombreImagen, \File::get($image));

                    $objImagenesRoles = new ImagenesRoles;
                    $objImagenesRoles->id_empleado   = $idEmpleado->party_id;
                    $objImagenesRoles->fecha_nomina  = Carbon::parse($request->fecha_nomina)->format('Y-m-05');
                    $objImagenesRoles->nombre_imagen = $nombreImagen;
                    $objImagenesRoles->tipo          = $request->tipo;

                    if ($objImagenesRoles->save()) {
                        $msg .= '<div class="alert alert-success" role="alert" style="margin: 10px">
                                Se ha guardado la imagen ' . $image->getClientOriginalName() . ' con éxito
                            </div>';
                        $status = 1;
                    } else {
                        $msg .= '<div class="alert alert-danger" role="alert" style="margin: 10px">
                                Hubo un error al trata de guardar la imagen con el nombre ' . $image->getClientOriginalName() . '
                            </div>';
                        $status = 0;
                    }
                }

            }else {
                $errores = '';
                foreach ($validaImagen->errors()->all() as $mi_error) {
                    if ($errores == '') {
                        $errores = '<li>' . $mi_error . '</li>';
                    } else {
                        $errores .= '<li>' . $mi_error . '</li>';
                    }
                }
                $msg .= '<div class="alert alert-danger">' .
                    '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                    '<ul>' .
                    $errores .
                    '</ul>' .
                    '</div>';
                $status = 0;
            }
        }
        return response()->json(['status'=>$status,'msg'=>$msg]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $deleteImagen = ImagenesRoles::find($request->id_imagen_rol)->delete();

        if($deleteImagen){
            Storage::disk('imagenes_roles')->delete($request->nombre_imagen);
            $msg = '<div class="alert alert-success" role="alert" style="margin: 10px">
                     Se ha eliminado la imagen con exito
               </div>';
            $status = 1;
        }else{
            $msg = '<div class="alert alert-alert" role="alert" style="margin: 10px">
                     Hubo un error al trata de eliminar la imagen, intente nuevamente
               </div>';
            $status = 1;
        }
        return response()->json(['status'=>$status,'msg'=>$msg]);
    }

    public function reporteRolesPago(Request $request){

        $dataRoles = ImagenesRoles::where('tipo',isset($request->tipo) ? $request->tipo : 1)
                        ->where('estado',true)->orderBy('fecha_nomina','Desc')->get();

        $dataCompuesta = [];
        $extension = [];
        $groupRoles=[];

        foreach ($dataRoles as $dR){


            $extension = explode(".",$dR->nombre_imagen);

            $nomina = Nomina::where([
                'nomina.id_empleado' => $dR->id_empleado,
                'nomina.liquidacion' => isset($request->tipo) ? ($request->tipo=='1' || $request->tipo==3 ? false : true) : false,
            ])->leftJoin('alcance_nomina as an','nomina.id_nomina','an.id_nomina')->where(function($q) use ($dR){

                $q->whereBetween('nomina.fecha_nomina', [
                    Carbon::parse($dR->fecha_nomina)->startOfMonth()->format('Y-m-d'),
                    Carbon::parse($dR->fecha_nomina)->endOfMonth()->format('Y-m-d')
                ]);

            })->join('contrataciones as c','nomina.id_contrataciones','c.id_contrataciones')
                ->join('tipo_contrato as tc','c.id_tipo_contrato','tc.id_tipo_contrato')
                ->select(
                    'nomina.*','c.*','tc.*','an.total as monto_alcance',
                    DB::raw("(select count(*) from referencia_pago where tipo='nomina' and id_registro = nomina.id_nomina) as pagado"))->first();


            if(isset($nomina)){

                if($request->tipo == 3){

                    $total=0;
                   // $totales =   // DB::select('select total as monto from alcance_nomina where id_nomina = ?', [$nomina->id_nomina]);
                    $total = $nomina->monto_alcance;
                    //dump($nomina);

                }else{
                    $total = $nomina->total;
                }

                $paymentMethod = PaymentMethod::join('eft_account as ea','payment_method.payment_method_id','ea.payment_method_id')
                                ->where('payment_method.party_id',$nomina->id_empleado)
                                ->whereNull('thru_date')->select('ea.account_number')->first();

                if($request->tipo == 2){

                    $nomina->pagado = ReferenciaPago::where([
                        'tipo' => 'liquidacion',
                        'id_registro' => $nomina->id_nomina
                    ])->exists();

                }

                if($request->estado == 0) {
                    if ($extension[1] == "pdf")
                        $dataCompuesta = [
                            'relacion_dependencia' => $nomina->relacion_dependencia,
                            'id_contrataciones' => $nomina->id_contrataciones,
                            'cuenta' => isset($paymentMethod) ? $paymentMethod->account_number : 'Sin Cuenta',
                            'id_nomina' => $nomina->id_nomina,
                            'nombre_empleado' =>  $nomina->persona,
                            'identificacion' => $nomina->identificacion,
                            'íd_empleado' => $dR->id_empleado,
                            'id_imagen_rol' => $dR->id_imagen_rol,
                            'fecha_nomina' => $dR->fecha_nomina,
                            'nombre_imagen' => $dR->nombre_imagen,
                            'id_empleado' => $dR->id_empleado,
                            'tipo_contrato' => $nomina->relacion_dependencia ? 'Relación de dependencia': 'Honorarios profesionales',
                            'monto' => $total,
                            "pagado" =>  $nomina->pagado
                        ];
                }
                if($request->estado == 1){
                    if ($extension[1] == "jpg" || $extension[1] == "JPG" || $extension[1] == "png" || $extension[1] == "PNG" || $extension[1] == "pdf")
                        $dataCompuesta = [
                            'relacion_dependencia' => $nomina->relacion_dependencia,
                            'id_contrataciones' => $nomina->id_contrataciones,
                            'cuenta' => isset($paymentMethod) ? $paymentMethod->account_number : 'Sin Cuenta',
                            'id_nomina' => $nomina->id_nomina,
                            'nombre_empleado' => $nomina->persona,
                            'identificacion'  => $nomina->identificacion,
                            'íd_empleado'     =>  $dR->id_empleado,
                            'id_imagen_rol'   => $dR->id_imagen_rol,
                            'fecha_nomina'    => $dR->fecha_nomina,
                            'nombre_imagen'   => $dR->nombre_imagen,
                            'id_empleado'     => $dR->id_empleado,
                            'tipo_contrato' => $nomina->relacion_dependencia ? 'Relación de dependencia': 'Honorarios profesionales',
                            'monto'  => $total,
                            "pagado" =>  $nomina->pagado
                        ];
                }

                $groupRoles[Carbon::parse($dataCompuesta["fecha_nomina"])->format('Y-m-d')][] = $dataCompuesta;
            }

        }

        return view('layouts.views.nomina.reporte_roles_pago',[
            'dataRoles' => manualPagination($groupRoles,10),
            'extension' => !empty($extension) ? $extension[1] : "",
            'tipo'      => isset($request->tipo) ? $request->tipo : 1,
            'cantAlcances' => AlcanceNomina::whereNotIn('id_alcance_nomina',function($query){
                $query->select('id_registro')->from('referencia_pago')->where('tipo','alcance_nomina');
            })->count() && $request->tipo == '3',
            'cantLiquidaciones' => Nomina::where('liquidacion',true)
            ->whereNotIn('id_nomina',function($query){
                $query->select('id_registro')->from('referencia_pago')->where('tipo','liquidacion');
            })->count() && $request->tipo == '2',

        ]);
    }

    public function reporteRolesPagoEmpleado(Request $request){

        $date = Carbon::now();
        $dataRoles = ImagenesRoles::where('id_empleado',session('dataUsuario')['id_empleado'])
                        ->orderBy('fecha_nomina','Desc')->get();

        $dataCompuesta = [];
        $extension = [];
        foreach ($dataRoles as $dR){

            $dataPerson = Person::where('person.party_id',$dR->id_empleado)
                ->leftJoin('party_identification as pi','person.party_id','pi.party_id')
                ->select('first_name','last_name','pi.id_value','person.party_id')->first();



            $extension = explode(".",$dR->nombre_imagen);
            if($request->estado == 0 ) {
                if ($extension[1] == "pdf")
                    $dataCompuesta[] = [
                        'nombre_empleado' => $dataPerson->first_name . " " . $dataPerson->last_name,
                        'identificacion' => $dataPerson->id_value,
                        'íd_empleado' => $dataPerson->party_id,
                        'id_imagen_rol' => $dR->id_imagen_rol,
                        'fecha_nomina' => $dR->fecha_nomina,
                        'nombre_imagen' => $dR->nombre_imagen,
                        'id_empleado' => $dR->id_empleado,
                    ];
            }
            if($request->estado == 1){
                if ($extension[1] == "jpg" || $extension[1] == "JPG" || $extension[1] == "png" || $extension[1] == "PNG")
                    $dataCompuesta[] = [
                        'nombre_empleado' => $dataPerson->first_name ." ".$dataPerson->last_name,
                        'identificacion'  => $dataPerson->id_value,
                        'íd_empleado'     => $dataPerson->party_id,
                        'id_imagen_rol'   => $dR->id_imagen_rol,
                        'fecha_nomina'    => $dR->fecha_nomina,
                        'nombre_imagen'   => $dR->nombre_imagen,
                        'id_empleado'     => $dR->id_empleado,
                    ];
            }

        }

        $groupRoles = [];
        foreach ($dataCompuesta as $dR){
            $groupRoles[Carbon::parse($dR["fecha_nomina"])->format('Y-m-d')][] = $dR;
        }

        return view('layouts.views.nomina.reporte_roles_pago_empleado',[
            'dataRoles' => manualPagination($groupRoles,10),
            'extension' => !empty($extension) ? $extension[1] : "",
            'tipo'      => isset($request->tipo) ? $request->tipo : 1
        ]);
    }

    public function estadisticaNomina(Request $request)
    {
        $dataRoles = Nomina::where('liquidacion',false);

        isset($request->fecha)
            ? $dataRoles->whereBetween('fecha_nomina', [$request->fecha.'-01-01', $request->fecha.'-12-31'])
            : $dataRoles->whereBetween('fecha_nomina', [now()->format('Y-01-01'), now()->format('Y-12-31')]);



        $groupRoles = [];
        foreach ($dataRoles->get() as $dR) {
            $groupRoles[Carbon::parse($dR["fecha_nomina"])->format('Y-m-d')][] = $dR;
        }

       $data = [];
        foreach ($groupRoles as $groupRole){
            $total = 0;
            foreach ($groupRole as $item) {
                $total += $item->total;
                $mes = intval(Carbon::parse($item->fecha_nomina)->format('m'));
            }
            $data[$mes-1] = [number_format($total,2,".","")];
        }


        $data2= [];
        for($a=0;$a<12;$a++){
            if(array_key_exists($a, $data))
                $data2[] = $data[$a][0];
            else
                $data2[] = 0;
        }

        return [
            $data2,
            $request->fecha,
            Nomina::select(DB::raw('EXTRACT(YEAR FROM fecha_nomina) as anno'))->distinct()->get()
            ];
    }

    public function formnivelarNomina(){

        $dataContrataciones = Contrataciones::join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')
            ->join('tipo_contrato as tc','contrataciones.id_tipo_contrato','tc.id_tipo_contrato')
            ->where([
                ['contrataciones.estado',1],
                ['contrataciones.id_tipo_contrato_descripcion',2],
            ])->get();


        if(count($dataContrataciones) < 1){
            return '<div class="alert alert-warning" role="alert" style="margin: 0">
                      <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                      No existen contrataciones activas para generar las nómina correspondientes
                        </div>';
                    }

        return view('layouts.views.nomina.partials.form_nivelar_nomina',[
            'dataContrataciones' => $dataContrataciones
        ]);
    }

    public function nivelarNomina(Request $request){

        ini_set('max_execution_time', 240);
        $arrDataContrataciones = [];
        $contrataciones = Contrataciones::join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')
                   ->join('tipo_contrato as tc', 'contrataciones.id_tipo_contrato','tc.id_tipo_contrato')
                   ->join('cargos as c','dc.id_cargo','c.id_cargo')
                   ->where([
                       ['contrataciones.id_tipo_contrato_descripcion',2],
                       ['contrataciones.estado',1],
                       ['dc.fecha_expedicion_contrato','<=',Carbon::now()->subDays(Carbon::now()->format('d'))->toDateString()]
                   ])->orderBy('id_detalle_contrataciones','desc')->get();

        foreach ($contrataciones as  $key => $contratacion){

            $fecha = null;

            if(isset($request->arrVacaciones)){
                foreach ($request->arrVacaciones as $vacaciones){
                    if((int)$vacaciones[0] == (int)$contratacion->id_contrataciones)
                        $fecha = $vacaciones[1];
                }
            }


            $complementoDecimoTercero = null;
            if(isset($request->arrDecimoTercero)){
                foreach ($request->arrDecimoTercero as $decimoTercero){
                    if((int)$decimoTercero[0] == (int)$contratacion->id_contrataciones)
                        $complementoDecimoTercero = $decimoTercero[1];
                }
            }


            $arrDataContrataciones[] = [
                'id_detalle_contratacion' => $contratacion->id_detalle_contrataciones,
                'fecha_expedicion_contrato' =>  $contratacion->fecha_expedicion_contrato,
                'id_empleado' => $contratacion->id_empleado,
                //'salario' =>//$contratacion->salario,
                'id_empleado' => $contratacion->id_empleado,
                'relacion_dependencia' => $contratacion->relacion_dependencia,
                'decimo_tercero' => $contratacion->decimo_tercero,
                'decimo_cuarto' => $contratacion->decimo_cuarto,
                'fondo_reserva' => $contratacion->fondo_reserva,
                'duracion' =>  $contratacion->duracion,
                'id_contrataciones' => $contratacion->id_contrataciones,
                'iva' => $contratacion->iva,
                'retencion_iva' => $contratacion->retencion_iva,
                'retencion_renta' => $contratacion->retencion_renta,
                'id_empleado' => $contratacion->id_empleado,
                'nombre' => $contratacion->nombre,
                'fecha_ultimas_vacaciones' => $fecha,
                'vacaciones' => $contratacion->vacaciones,
                'complementoDecimoTercero' => $complementoDecimoTercero,
                'id_contratacion' =>$contratacion->id_contrataciones
            ];
        }
        arsort($arrDataContrataciones);

        foreach ($arrDataContrataciones as $key => $contrataciones) {

            $diasTranscurridos = Carbon::parse($contrataciones['fecha_expedicion_contrato'])->diffInDays(Carbon::now()->toDateString());
            $mesesTranscurridos = ($diasTranscurridos + 1) / 30;
            $mesesTranscurridos > 1 ? $mesesTranscurridos +=1 : "";


                if($contrataciones['relacion_dependencia'] == true){
                    $dateExpedicionContrato = Carbon::parse(Carbon::now()->subYear(1)->format('Y-08-01'));
                    if(Carbon::parse($contrataciones['fecha_expedicion_contrato'])->format('Y-m') > Carbon::now()->subYear(1)->format('Y-08'))
                        $dateExpedicionContrato = Carbon::parse($contrataciones['fecha_expedicion_contrato']);
                }else{
                    $dateExpedicionContrato = Carbon::parse($contrataciones['fecha_expedicion_contrato']);
                }



            for ($i=1; $i <= $mesesTranscurridos; $i++) {

                $x=0;

                if((($dateExpedicionContrato->format('Y-m') != Carbon::now()->subMonth(1)->format('Y-m'))) || ($i == 1 && $dateExpedicionContrato->format('Y-m') == Carbon::now()->subMonth(1)->format('Y-m'))){ // PARA QUE SOLO LLEGUE HASTA EL MES ANTERIOR DEL ACTUAL

                    $i > 1 ? $dateExpedicionContrato->addMonth(1) : "";
                    $inicioContratacion = $contrataciones['fecha_expedicion_contrato'];
                    $ultimoDia = getUltimoDiaMes($dateExpedicionContrato);
                    $salario = getSalarioEmpleado($contrataciones['id_empleado'],$dateExpedicionContrato);//$contrataciones['salario'];
                    $inicio = Carbon::parse($dateExpedicionContrato)->format('Y-m-01');
                    $fin = Carbon::parse($dateExpedicionContrato)->format('Y-m-'.$ultimoDia);

                    /*$contrataciones['relacion_dependencia'] == true
                        ? $inicioCalculo = Carbon::now()->subYear(1)->format('Y-08-01')
                        : $inicioCalculo = $contrataciones['fecha_expedicion_contrato'];*/

                    if (Carbon::parse($contrataciones['fecha_expedicion_contrato'])->toDateString() === $dateExpedicionContrato->toDateString()) { //ITERACIÓN DEL PRIMER MES
                        $ultimoDia = getUltimoDiaMes($inicioContratacion);
                        $finMesInicioContrato = Carbon::parse($contrataciones['fecha_expedicion_contrato'])->format('Y-m-'.$ultimoDia);

                        if(Carbon::parse($finMesInicioContrato)->format('d') == 31 || (Carbon::parse($finMesInicioContrato)->format('m')== 02 && Carbon::parse($finMesInicioContrato)->format('d') == 28) || (Carbon::parse($finMesInicioContrato)->format('m')== 02 && Carbon::parse($finMesInicioContrato)->format('d') == 29))
                               $finMesInicioContrato =  Carbon::parse($finMesInicioContrato)->format('Y-m-30');


                        $inicio = Carbon::parse($dateExpedicionContrato)->format('Y-m-d');
                    }

                    //$salario            = $salario;
                    $horasExtras        = getHorasExtras($contrataciones['id_empleado'], 1, $inicio, $fin);
                    $comisiones         = getComisiones($contrataciones['id_empleado'], $inicio, $fin);
                    $consumos           = getConsumos($contrataciones['id_empleado'], 1, true, $fin);
                    $dataAnticipos      = getAnticipos($contrataciones['id_empleado'], 1, true, $fin);
                    $anticipos       = $dataAnticipos['montoAnticipos'];
                    $otrosDescuentos    = getOtrosDescuentos($contrataciones['id_empleado'], 1, true, $fin);
                    $decimoTercero      = 0.00;
                    $decimoCuarto       = 0.00;
                    $fondoReserva       = 0.00;
                    $aportePersonalIESS = 0.00;

                    if ($contrataciones['relacion_dependencia']){

                        $date = Carbon::parse($dateExpedicionContrato);

                        //////// DECIMO TERCERO ////////
                        if ($contrataciones['decimo_tercero'] && $contrataciones['decimo_tercero'] != null) {//Mensualizado
                            $decimoTercero = getDecimoTercero($salario, $horasExtras, $contrataciones['id_empleado'], true, $date->addMonth(1)->format('Y-m-d'));
                        } else {
                            $date = Carbon::parse($dateExpedicionContrato);
                            if ($dateExpedicionContrato->format('Y-m-01') >= $dateExpedicionContrato->format('Y-12-01') && $dateExpedicionContrato->format('Y-m-01') <= $dateExpedicionContrato->format('Y-12-31')){
                                $decimoTercero = getDecimoTerceroAnual($contrataciones['id_empleado'], true, $date->addMonth(1)->format('Y-m-d')) + ($contrataciones['complementoDecimoTercero'] != null ? $contrataciones['complementoDecimoTercero'] : 0 );
                            }
                        }
                        //////// FIN DECIMO TERCERO ////////

                        /////////// DECIMO CUARTO ////////
                        if ($contrataciones['decimo_cuarto'] && $contrataciones['decimo_cuarto'] != null) {//Mensualizado
                            $decimoCuarto = getDecimoCuarto();
                        } else {
                            if ($dateExpedicionContrato->format('Y-m-01') >= $dateExpedicionContrato->format('Y-08-01') && $dateExpedicionContrato->format('Y-m-01') <= $dateExpedicionContrato->format('Y-08-31'))
                                $decimoCuarto = getDecimoCuartoAnual($contrataciones['id_empleado'], true, $dateExpedicionContrato->format('Y-m-d'));
                        }
                        ////////// FIN DECIMO CUARTO ////////

                        ////////// FONDO DE RESERVA //////////
                        if (Carbon::parse($dateExpedicionContrato)->diffInYears($contrataciones['fecha_expedicion_contrato']) >= getConfiguracionEmpresa()->anno_calculo_fondo_reserva)
                            if ($contrataciones['fondo_reserva']) {//Mensualizado
                                $fondoReserva = getFondoReserva($salario, $horasExtras);
                            } else {
                                if((Carbon::parse($dateExpedicionContrato)->format("m") == Carbon::parse($contrataciones['fecha_expedicion_contrato'])->format('m')))
                                    $fondoReserva = getFondoReservaAnual($contrataciones['id_empleado']);
                            }
                        ////////// FIN FONDO DE RESERVA //////////

                        $aportePersonalIESS = getAportePersonal($salario, $horasExtras,$contrataciones['id_contrataciones']);

                        ////////  DIAS VACACIONES  /////////

                        if($contrataciones['fecha_ultimas_vacaciones'] != null){

                            $diasTranscurridosUltimasVacaciones = Carbon::parse($contrataciones['fecha_ultimas_vacaciones'])->diffInDays(Carbon::now()->toDateString());
                            $diasVacaciones = ConfiguracionVariablesEmpresa::select('vacaciones_dias_entre_semana','vacaciones_dias_fines_semana')->first();
                            $diasVacaciones = $diasVacaciones->vacaciones_dias_entre_semana + $diasVacaciones->vacaciones_dias_fines_semana;
                            $diasVacacionesAcumuladas = ($diasVacaciones/360)*($diasTranscurridosUltimasVacaciones+1);

                            $objDetalleContratacion = DetalleContratacion::find($contrataciones['id_detalle_contratacion']);
                            $objDetalleContratacion->vacaciones = $contrataciones['vacaciones'] + $diasVacacionesAcumuladas;
                            $objDetalleContratacion->save();
                        }
                        //////// FIN DIAS VACACIONES /////////
                    }else {

                        if ($contrataciones['duracion'] > 0) {

                            $fechaTerminacionContratacion = Carbon::parse($contrataciones['fecha_expedicion_contrato'])->addDay($contrataciones['duracion'])->toDateString();
                            $diasLaboradosUltimoMes = Carbon::parse($fechaTerminacionContratacion)->format('d');
                            $ultimoMesLaborado = Carbon::parse($fechaTerminacionContratacion)->format('m');
                            if (($ultimoMesLaborado == "02" && $diasLaboradosUltimoMes == 28 || $ultimoMesLaborado == "02" && $diasLaboradosUltimoMes == 29) || $diasLaboradosUltimoMes == 31)
                                $diasLaboradosUltimoMes = 30;

                            if (Carbon::parse($fechaTerminacionContratacion)->toDateString() <= $dateExpedicionContrato->toDateString() && Carbon::parse($fechaTerminacionContratacion)->toDateString() >= $dateExpedicionContrato->toDateString())
                                $salario = number_format(($salario / 30) * $diasLaboradosUltimoMes, 2, ".", "");

                            if (Carbon::parse($contrataciones['fecha_expedicion_contrato'])->diffInDays($dateExpedicionContrato) >= $contrataciones['duracion'] + $diasLaboradosUltimoMes) { //A LIQUIDAR (REVISAR LOS BONOS Y PRESTAMOS)
                                $x = 1;
                                $objContratacionesConfidencialidad = Contrataciones::find($contrataciones['id_contrataciones']);
                                $objContratacionesConfidencialidad->estado = 3;
                                $objContratacionesConfidencialidad->save();

                                $objForeginContratacionesConfidencialidad = ForeginContrataciones::find($contrataciones['id_contrataciones']);
                                $objForeginContratacionesConfidencialidad->estado = 3;
                                $objForeginContratacionesConfidencialidad->save();

                                $objFinalizacionContratacion = new FinalizacionContratacion;
                                $objFinalizacionContratacion->id_contrataciones = $contrataciones['id_contrataciones'];
                                $objFinalizacionContratacion->id_tipo_finalizacion = 1;
                                $objFinalizacionContratacion->fecha_finalizacion = $fechaTerminacionContratacion;
                                $objFinalizacionContratacion->save();

                            }
                        }
                    }

                    if($x == 0){

                        $arrBonosFijos = getBonosFijos($contrataciones['id_contrataciones'],false,0,$dateExpedicionContrato->format('Y-m-05'));
                        $arrPrestamos  = getPrestamos($contrataciones['id_contrataciones'],1,false,$dateExpedicionContrato->format('Y-m-05'));

                        $ingresos = $salario + $horasExtras + $comisiones + $decimoTercero + $decimoCuarto + $fondoReserva + $arrBonosFijos['montoBonosFijos'];
                        $egresos  = $aportePersonalIESS + $consumos + $anticipos + $otrosDescuentos['totalOtrosDescuentos'] + $arrPrestamos['montoPrestamos'];
                        $subTotal = $ingresos - $egresos;

                        $total          = $subTotal;
                        $iva            = false;
                        $retencionIva   = false;
                        $retencionRenta = false;
                        if(!$contrataciones['relacion_dependencia']){
                            $iva            = $subTotal*($contrataciones['iva']/100);
                            $retencionIva   = $iva*($contrataciones['retencion_iva']/100);
                            $retencionRenta = $subTotal*($contrataciones['retencion_renta']/100);
                            $total          = $subTotal + $iva - $retencionIva - $retencionRenta;
                        }

                        $objNomina = new Nomina;
                        $objNomina->id_empleado = $contrataciones['id_empleado'];
                        $objNomina->fecha_nomina = Carbon::parse($dateExpedicionContrato)->format("Y-m-05");
                        $objNomina->id_contrataciones = $contrataciones['id_contrataciones'];
                        $objNomina->total = $total;

                        if ($objNomina->save()) {
                            if ($contrataciones['relacion_dependencia']) {

                                $model = Nomina::all()->last();
                                $decimoTerceroMensual = getDecimoTercero($salario, $horasExtras, $contrataciones['id_empleado']);
                                $vacacionMensual      = number_format(getVacaciones($salario, $horasExtras), 2, ".", "");
                                $fondoReservaMensual  = getFondoReserva($salario, $horasExtras);
                                saveDataMensualRelacionDependecia($contrataciones,$model,$decimoTerceroMensual,$vacacionMensual,$fondoReservaMensual,$dateExpedicionContrato);

                            }
                        }

                        $dataEmpleado = Person::where('person.party_id', $contrataciones['id_empleado'])
                            ->join('party_identification as pi', 'person.party_id', 'pi.party_id')
                            ->join('party_identification_type as pit', 'pi.party_identification_type_id', 'pit.party_identification_type_id')
                            ->select('first_name','last_name','id_value','pit.description','person.party_id')->first();

                        $dataRolIndividual = [
                            'nombre_empleado'      => $dataEmpleado->first_name . " " . $dataEmpleado->last_name,
                            'cargo'                => $contrataciones['nombre'],
                            'documento'            => $dataEmpleado->description,
                            'identificacion'       => $dataEmpleado->id_value,
                            'id_empleado'          => $dataEmpleado->party_id,
                            'salario'              => $salario,
                            'horas_extras'         => $horasExtras,
                            'comisiones'           => $comisiones,
                            'decimo_tercero'       => $contrataciones['relacion_dependencia'] == 1 ? $decimoTercero : 'N/A',
                            'decimo_cuarto'        => $contrataciones['relacion_dependencia'] == 1 ? $decimoCuarto : 'N/A',
                            'fondo_reserva'        => $contrataciones['relacion_dependencia'] == 1 ? $fondoReserva : 'N/A',
                            'aporte_personal_IESS' => $contrataciones['relacion_dependencia'] == 1 ? $aportePersonalIESS : 'N/A',
                            'consumos'             => $consumos,
                            'anticipos'            => $anticipos,
                            'otros_descuentos'     => $otrosDescuentos['arrOtrosDescuentos'],
                            'arrPrestamos'         => $arrPrestamos['arrPrestamos'],
                            'arr_bonos_fijos'      => $arrBonosFijos['arrBonosFijos'],
                            'iva'                  => $iva,
                            'retencionIva'         => $retencionIva,
                            'retencionRenta'       => $retencionRenta,
                            'ingresos'             => $ingresos + $iva,
                            'egresos'              => $egresos + $retencionIva + $retencionRenta,
                            'total'                => $total,
                            'consecutivos'         => true,
                            'fecha'                => $dateExpedicionContrato,
                            'id_contratacion'      => $contrataciones['id_contrataciones']
                        ];


                        $nombreRol = 'rol-' . Carbon::parse($dateExpedicionContrato)->format("Y-m-05") . '-' . $i . '-(' . $dataEmpleado->first_name . "-" . $dataEmpleado->last_name . ').pdf';
                        $objImagenesRoles = new ImagenesRoles;
                        $objImagenesRoles->id_empleado   = $contrataciones['id_empleado'];
                        $objImagenesRoles->fecha_nomina  = Carbon::parse($dateExpedicionContrato)->format('Y-m-05');
                        $objImagenesRoles->nombre_imagen = $nombreRol;

                        if($objImagenesRoles->save()){

                            $view = \View::make('layouts.views.nomina.recibo_pago_individual_pdf', compact('dataRolIndividual'))->render();
                            $pdf = \App::make('dompdf.wrapper');
                            $pdf->loadHTML($view);
                            $pdf->save(public_path('roles_pago').'/'.$nombreRol);
                        }
                    }
               }
            }
        }
        return '<div class="alert alert-success" role="alert" style="margin: 0">
                  <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                  Las nóminas correspondientes han sido generadas hasta el mes actual, puede ver los roles de pagos haciendo clic en el siguiente enlace: <br />
                  <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                  <a href="'.route('vista.roles-pago',['estado'=>0]).'">
                    Roles de pago
                  </a>
               </div>';

    }

    public function vistaNomina(Request $request){
        return view('layouts.views.nomina.partials.vista_nomina');
    }

    public function generaNomina(Request $request){

        ini_set('max_execution_time', 240);
        DB::beginTransaction();

        try{

            if(!isset($request->fecha))
                return '<div class="alert alert-warning text-center"><i class="fa fa-times-circle"></i> Seleccione una fecha válida</div>';

            if($request->store == 1){
                $date = Carbon::parse($request->fecha);

                $existNomina = Nomina::whereBetween('fecha_nomina',[$date->format('Y-m-01'),$date->endOfMonth()->toDateString()])
                    ->where('liquidacion',false)->count();
                if($existNomina > 0)
                    return '<div class="alert alert-warning"><i class="fa fa-times-circle"></i> La nómina perteneciente a la fecha seleccionada ya está realizada puede ver los roles de pagos generados desde el menu Nómina -> Roles de nómina</div>';
            }

            $dataContrataciones = Contrataciones::join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')
                ->join('tipo_contrato as tc', 'contrataciones.id_tipo_contrato','tc.id_tipo_contrato')
                ->where([
                    ['contrataciones.id_tipo_contrato_descripcion',2],
                    ['contrataciones.estado',1],
                    ['dc.fecha_expedicion_contrato','<=',Carbon::parse($request->fecha)/*->subDays(Carbon::parse($request->fecha)->format('d'))*/]
                ]);

            if(isset($request->id_empleado) && $request->store == 2)
                $dataContrataciones->where('contrataciones.id_empleado',$request->id_empleado);

            $dataContrataciones = $dataContrataciones->get();

            if(count($dataContrataciones)  == 0)
                return '<div class="alert alert-info"><i class="fa fa-times-circle"></i> No exiten contrataciones activas para realizar una nómina o las contrataciones actuales no cumplen un mes de vigencia aún</div>';

            $date      = Carbon::parse($request->fecha)/*->subMonth(1)*/;
            $ultimoDia = getUltimoDiaMes($date->format('Y-m-d'));
            $inicio    = $date->format('Y-m-01');
            $fin       = $date->format('Y-m-'.$ultimoDia);
            $dataVistaRolesGeneral = [];

            foreach ($dataContrataciones as $key => $contrataciones) {

                $salario            = getSalarioEmpleado($contrataciones->id_empleado,$inicio);
                $horasExtras        = getHorasExtras($contrataciones->id_empleado,$request->store,$inicio,$fin);
                $comisiones         = getComisiones($contrataciones->id_empleado,$inicio,$fin,$request->store);
                $decimoTercero      = 0.00;
                $decimoCuarto       = 0.00;
                $fondoReserva       = 0.00;
                $aportePersonalIESS = 0.00;
                $aportePatronalIESS = 0.00;
                $totalPrestamo      = 0.00;
                $persona = getPerson($contrataciones->id_empleado);

                if($contrataciones->relacion_dependencia){

                    $fechaActual = $date->format('Y-m-d');

                    //////// DECIMO TERCERO ////////
                    $decimoTerceroMensual = getDecimoTercero($salario,$horasExtras,$contrataciones->id_empleado,false,$date);

                    if($contrataciones->decimo_tercero){//Mensualizado

                        $decimoTercero = $decimoTerceroMensual;

                    }else{ // Anualizado

                        //if($fechaActual >= Carbon::parse($request->fecha)->format('Y-12-01') && $fechaActual <= Carbon::parse($request->fecha)->format('Y-12-31'))
                            //$decimoTercero = getDecimoTerceroAnual($contrataciones->id_empleado,false,$request->store);

                    }
                    //////// FIN DECIMO TERCERO ////////


                    /////////// DECIMO CUARTO ////////
                    if($contrataciones->decimo_cuarto){//Mensualizado

                        $decimoCuarto  = getDecimoCuarto();

                    }else{ // Anualizado

                       // if($fechaActual >= Carbon::parse($request->fecha)->format('Y-08-01') && $fechaActual <= Carbon::parse($request->fecha)->format('Y-08-15'))
                            //$decimoCuarto = getDecimoCuartoAnual($contrataciones->id_empleado,true);

                    }

                    ////////// FIN DECIMO CUARTO ////////


                    ////////// FONDO DE RESERVA //////////
                    if(Carbon::parse($request->fecha)->diffInYears($contrataciones->fecha_expedicion_contrato) >= getConfiguracionEmpresa()->anno_calculo_fondo_reserva)
                        if($contrataciones->fondo_reserva){//Mensualizado

                            $fondoReserva = getFondoReserva($salario,$horasExtras);

                        }else{

                            //$fondoReserva = getFondoReservaAnual($contrataciones->id_empleado);

                        }
                    ////////// FIN FONDO DE RESERVA //////////

                    $aportePersonalIESS = getAportePersonal($salario,$horasExtras,$contrataciones->id_contrataciones,$fin);
                    $aportePatronalIESS = getAportePatronal($salario,$horasExtras,$contrataciones->id_contrataciones,$fin);

                }

                $dataEmpleado = Person::where('person.party_id',$contrataciones->id_empleado)
                    ->join('party_identification as pi','person.party_id','pi.party_id')
                    ->join('party_identification_type as pit','pi.party_identification_type_id','pit.party_identification_type_id')
                    ->select('person.first_name','person.last_name','pit.description','pi.id_value','person.party_id')
                    ->first();

                $arrBonosFijos   = getBonosFijos($contrataciones->id_contrataciones,false,0,$fin);
                $arrPrestamos    = getPrestamos($contrataciones->id_contrataciones,$request->store,false,$fin);
                $arrAnticipos    = getAnticipos($contrataciones->id_empleado,$request->store,false,$fin);
                $consumos        = getConsumos($contrataciones->id_empleado,$request->store,false,$fin);
                $anticipos       = $arrAnticipos['montoAnticipos'];
                $otrosDescuentos = getOtrosDescuentos($contrataciones->id_empleado,$request->store,false,$fin);

                $ingresos = $salario + $horasExtras + $comisiones + $decimoTercero + $decimoCuarto + $fondoReserva + $arrBonosFijos['montoBonosFijos'];
                $egresos  = $aportePersonalIESS + $consumos + $anticipos + $otrosDescuentos['totalOtrosDescuentos'] + $arrPrestamos['montoPrestamos'];

                $subTotal = $ingresos - $egresos;

                $total = $subTotal;
                $iva            = false;
                $retencionIva   = false;
                $retencionRenta = false;
                $sumatoriaBase = $arrBonosFijos['montoBonosFijos'] + $salario + $comisiones + $horasExtras;

                if(!$contrataciones->relacion_dependencia){
                    $iva            = $contrataciones->retencion_iva > 0 ? ($sumatoriaBase)*($contrataciones->iva/100) : 0;
                    $retencionIva   = $contrataciones->retencion_iva > 0 ? $iva*($contrataciones->retencion_iva/100) : 0;
                    $retencionRenta = $contrataciones->retencion_renta > 0 ? ($sumatoriaBase)*($contrataciones->retencion_renta/100) : 0;
                    $total          = $subTotal + $iva - $retencionIva - $retencionRenta;
                }

                if($request->store == 1){

                    $idNomina = Nomina::orderBy('id_nomina','desc')->select('id_nomina')->first();

                    $objNomina = new Nomina;
                    $objNomina->id_nomina = isset($idNomina) ? $idNomina->id_nomina+1 : 1;
                    $objNomina->id_empleado = $contrataciones->id_empleado;
                    $objNomina->fecha_nomina = $request->fecha;
                    $objNomina->id_contrataciones = $contrataciones->id_contrataciones;
                    $objNomina->total = $total;
                    $objNomina->persona = $dataEmpleado->first_name." ".$dataEmpleado->last_name;
                    $objNomina->identificacion = $dataEmpleado->id_value;

                    if($objNomina->save()){
                        $model = Nomina::orderBy('id_nomina','desc')->first();

                        //PROVISIONAR BENEFICIOS SOCIALES
                        if($contrataciones->relacion_dependencia){
                            $vacacionMensual = number_format(getVacaciones($salario, $horasExtras), 2, ".", "");
                            $liquidacion = saveDataMensualRelacionDependecia(
                                $contrataciones,
                                $model,
                                $decimoTerceroMensual,
                                $vacacionMensual,
                                $fondoReserva,
                                $aportePatronalIESS,
                                $aportePersonalIESS,
                               // Carbon::parse($request->fecha)->diffInYears($contrataciones->fecha_expedicion_contrato) >= getConfiguracionEmpresa()->anno_calculo_fondo_reserva
                            );
                        }
                    }

                }

                $dataVistaRolesGeneral[]=[
                    'id_nomina'            => isset($model->id_nomina) ? $model->id_nomina : '',
                    'nombre_empleado'      => $dataEmpleado->first_name." ". $dataEmpleado->last_name,
                    'cargo'                => getCargo($dataEmpleado->party_id)->nombre, //Cargo::where('id_cargo',$contrataciones->id_cargo)->first()->nombre,
                    'documento'            => $dataEmpleado->description,
                    'identificacion'       => $dataEmpleado->id_value,
                    'id_empleado'          => $dataEmpleado->party_id,
                    'salario'              => $salario,
                    'horas_extras'         => $horasExtras,
                    'comisiones'           => $comisiones,
                    'decimo_tercero'       => $contrataciones->relacion_dependencia == 1 ? $decimoTercero : 'N/A',
                    'decimo_cuarto'        => $contrataciones->relacion_dependencia == 1 ? $decimoCuarto : 'N/A',
                    'fondo_reserva'        => $contrataciones->relacion_dependencia == 1 ? $fondoReserva : 'N/A',
                    'aporte_personal_IESS' => $contrataciones->relacion_dependencia == 1 ? $aportePersonalIESS : 'N/A',
                    'aporte_patronal_IEES' => $contrataciones->relacion_dependencia == 1 ? $aportePatronalIESS : 'N/A',
                    'consumos'             => $consumos,
                    'anticipos'            => $anticipos,
                    'otros_descuentos'     => $otrosDescuentos['arrOtrosDescuentos'],
                    'arrPrestamos'         => $arrPrestamos['arrPrestamos'],
                    'arr_bonos_fijos'      => $arrBonosFijos['arrBonosFijos'],
                    'monto_bonos_fijos'    => $arrBonosFijos['montoBonosFijos'],
                    'monto_prestamos'      => $arrPrestamos['montoPrestamos'],
                    'monto_descuentos'     => $otrosDescuentos['totalOtrosDescuentos'],
                    'iva'                  => $iva,
                    'retencionIva'         => $retencionIva,
                    'retencionRenta'       => $retencionRenta,
                    'ingresos'             => $ingresos + $iva,
                    'Egresos'              => $egresos + $retencionIva + $retencionRenta,
                    'total'                => number_format($total,2,".",""),
                    'id_contratacion'      => $contrataciones->id_contrataciones,
                    'fecha'                => $request->fecha,
                    'relacion_dependencia' => $contrataciones->relacion_dependencia
                ];

                if($request->store == 1 || (isset($request->id_empleado) && $request->store == 2)) {

                    $dataRolIndividual = [
                        'nombre_empleado'       => $dataEmpleado->first_name . " " . $dataEmpleado->last_name,
                        'cargo'                 => getCargo($dataEmpleado->party_id)->nombre, //Cargo::where('id_cargo', $contrataciones->id_cargo)->first()->nombre,
                        'documento'             => $dataEmpleado->description,
                        'identificacion'        => $dataEmpleado->id_value,
                        'id_empleado'           => $dataEmpleado->party_id,
                        'salario'               => $salario,
                        'horas_extras'          => $horasExtras,
                        'decimo_tercero'        => $contrataciones->relacion_dependencia == 1 ? $decimoTercero : 'N/A',
                        'decimo_cuarto'         => $contrataciones->relacion_dependencia == 1 ? $decimoCuarto : 'N/A',
                        'fondo_reserva'         => $contrataciones->relacion_dependencia == 1 ? $fondoReserva : 'N/A',
                        'aporte_personal_IESS'  => $contrataciones->relacion_dependencia == 1 ? $aportePersonalIESS : 'N/A',
                        'aporte_patronal_IEES'  => $contrataciones->relacion_dependencia == 1 ? $aportePatronalIESS : 'N/A',
                        'consumos'              => $consumos,
                        'anticipos'             => $anticipos,
                        'comisiones'            => $comisiones,
                        'otros_descuentos'      => $otrosDescuentos['arrOtrosDescuentos'],
                        'arrPrestamos'          => $arrPrestamos['arrPrestamos'],
                        'arr_bonos_fijos'       => $arrBonosFijos['arrBonosFijos'],
                        'iva'                   => $iva,
                        'retencionIva'          => $retencionIva,
                        'retencionRenta'        => $retencionRenta,
                        'ingresos'              => $ingresos + $iva,
                        'egresos'               => $egresos + $retencionIva +$retencionRenta,
                        'consecutivos'          => false,
                        'total'                 => number_format($total,2,".",""),
                        'id_contratacion'       => $contrataciones->id_contrataciones,
                        'fecha'                 => $request->fecha
                    ];

                    if ($request->store == 1){
                        $nombreRol = 'rol-' . Carbon::parse($request->fecha)->format('Y-m-05') . '-' . ($key + 1) . '-(' . $dataEmpleado->first_name . "-" . $dataEmpleado->last_name . ').pdf';
                        $objImagenesRoles = new ImagenesRoles;
                        $objImagenesRoles->id_empleado   = $contrataciones->id_empleado;
                        $objImagenesRoles->fecha_nomina  = Carbon::parse($request->fecha)->format('Y-m-05');
                        $objImagenesRoles->nombre_imagen = $nombreRol;

                        $objNomina = Nomina::find($objNomina->id_nomina);

                        if($contrataciones->relacion_dependencia){

                            $res = $this->generaFacturaRelacionDependencia([
                                'contrataciones' => $contrataciones,
                                'date' => $date,
                                'base' => $salario,
                                'horasExtras' => $horasExtras,
                                'bonos' => $arrBonosFijos['montoBonosFijos'],
                                'comisiones' => $comisiones,
                                'fondoReserva' => $fondoReserva,
                                'decimoTercero' => $decimoTercero,
                                'decimoCuarto' => $decimoCuarto,
                                'vacaciones' => $liquidacion['vacacion'],
                                'prestamos' => $arrPrestamos['arrPrestamos'],
                                'anticipos' => $arrAnticipos['arrAnticipos'],
                                'otrosDescuentos' => $otrosDescuentos['arrOtrosDescuentos'],
                                'aportePersonalIESS' => $aportePersonalIESS,
                                'total' => $total,
                                'descripcion_invoice' => 'FACTURA DE COMPRA GENERADA DESDE EL MÓDULO DE NÓMINA',
                                'descripcion_invoice_item' => 'INGRESOS BASE DE ROL DE PAGO',
                                'descripcion_hora_extra' => 'HORAS EXTRAS DEL ROL DE PAGO',
                                'descripcion_bono' => 'BONOS DEL ROL DE PAGO',
                                'descripcion_comision'=> 'COMISIONES DEL ROL DE PAGO',
                                'descripcion_fondo_reserva' => 'FONDO DE RESERVA DEL ROL DE PAGO',
                                'descripcion_dcmo_3er' => 'DECIMO TERCER SUELDO DEL ROL DE PAGO',
                                'descripcion_dcmo_4to' => 'DECIMO CUARTO SUELDO DEL ROL DE PAGO',
                                'descripcion_ancticipo' => 'DESCUENTO DE ANTICIPO SUELDO'
                            ]);

                            if(!$res['success'])
                                throw new \Exception($res['msg']);

                            $objNomina->update(['id_factura' => $res['invoiceId']]);

                        }else{

                            $res = $this->generaFacturaHonorarios([
                                'id_nomina' => $model->id_nomina,
                                'honorarios' => $sumatoriaBase,
                                'contrataciones' => $contrataciones,
                                'date' => $date,
                                'fin' => $fin,
                                'iva' => $iva,
                                'prestamos' => $arrPrestamos['arrPrestamos'],
                                'anticipos' => $arrAnticipos['arrAnticipos'],
                                'otrosDescuentos' => $otrosDescuentos['arrOtrosDescuentos'],
                                'salario' => $salario,
                                'descripcion_invoice_item' => 'INGRESOS DE NÓMINA'
                            ]);

                            if(!$res['success'])
                                throw new \Exception($res['msg']);

                               // dd($res['invoiceId']);
                            $objNomina->update(['id_factura' => count($res['invoiceId']) > 1 ? implode(',',$res['invoiceId']) : $res['invoiceId'][0]]);

                        }



                        if($objImagenesRoles->save()){
                            $view = \View::make('layouts.views.nomina.recibo_pago_individual_nomina_pdf', compact('dataRolIndividual'))->render();
                            $pdf = \App::make('dompdf.wrapper');
                            $pdf->loadHTML($view);
                            $pdf->save(public_path('roles_pago') . '/'.$nombreRol);
                        }

                    }else if($request->store == 2){

                        return view('layouts.views.nomina.partials.rol_empleado_nomina', [
                            'dataVistaNomina' => $dataRolIndividual,
                            'fecha' => $request->fecha
                        ]);

                    }
                }

            }

            if($request->store == 1 && count($dataVistaRolesGeneral)>0)

                if(!$this->storeNominaLineal($dataVistaRolesGeneral)){
                    $NominasGuardadas = Nomina::where('fecha_nomina',$request->fecha);
                    $NominasGuardadas->delete();
                    DB::rollBack();
                    return '<div class="alert alert-danger"><i class="fa fa-times-circle"></i> Ha ocurrdio un error mientras se guardaba la nómina, intente nuevamente por favor</div>';
                }

            if($request->store == 0) {
                DB::commit();
                return view('layouts.views.nomina.partials.listado_nomina', [
                    'dataVistaNomina' => $dataVistaRolesGeneral,
                    'fecha' => $request->fecha,
                    'aprobar' => true
                ]);
            }else if($request->store == 1){
                $view =  \View::make('layouts.views.nomina.recibo_pago_general_pdf',compact('dataVistaRolesGeneral'))->render();
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $number = mt_rand();
                $pdf->save(public_path('roles_pago').'/-rol'.$number.'('.$dataEmpleado->first_name." ". $dataEmpleado->last_name.').pdf');
                DB::commit();
                return '<div class="alert alert-success"><i class="fa fa-check"></i> La nómina perteneciente a la fecha seleccionada se ha generado con éxito</div>';
            }

        }catch(\Exception $e){
            DB::rollBack();
            return '<div class="alert alert-danger">
                        <i class="fa fa-times-circle"></i>
                        Ha ocurrdio un error mientras se guardaba la nómina<br />'.$e->getMessage().' '.$e->getFile().' '.$e->getLine().'
                    </div>';

        }

    }

    public function informeNomina(){
        return view('layouts.views.nomina.partials.informe_nomina');
    }

    public function generaInformeNomina(Request $request){

        $contrataciones = Contrataciones::where([
            ['estado',1],
            ['id_tipo_contrato_descripcion',2]
        ])->orderBy('id_contrataciones','asc')->get();

        $date      = Carbon::parse($request->fecha_nomina);
        $inicio    = $date->format('Y-m-01');
        $fin       = $date->endOfMonth()->toDateString();
        $arrDataInformeNomina =[];
        $fechaActual = now()->toDateString();

        $nominasPasadas = NominasPasadas::whereIn('fecha_nomina',[$inicio,$fin])->get();

        if(isset($nominasPasadas) && $nominasPasadas->count()>0){
            foreach($nominasPasadas as $np){
                $arrDataInformeNomina[] =[
                    'empleado' =>  $np->nombre,
                    'identificacion' =>$np->identificacion,
                    'cargo' =>$np->cargo,
                    'sueldo' =>$np->sueldo,
                    'H_E_50' => $np->he_50,
                    'H_E_100' => $np->he_100,
                    'comsiones' => $np->comisiones,
                    'Bonos'=>$np->bonos,
                    'iva' => $np->iva,
                    '10mo_3ero' => $np->decimo_3ero,
                    '10mo_4to' => $np->decimo_4to,
                    'fondo_reserva' => $np->fondo_reserva,
                    'aporte_patronal' => $np->apt_patronal,
                    'anticipos' => $np->anticipos,
                    'consumos' => $np->consumos,
                    'descuento'=> $np->descuentos,
                    'retencion_iva' => $np->ret_iva,
                    'retencion_renta' => $np->ret_renta,
                    'aporte_personal' => $np->apt_personal,
                    'prestamos'=>  $np->pPrestamos,
                    'total' => $np->total
                ];
            }
        }else if($nominasPasadas->count()==0 && (now()->format('m') == Carbon::parse($request->fecha_nomina)->format('m')) || now()->subMonth(1)->format('m') == Carbon::parse($request->fecha_nomina)->format('m')){
            foreach ($contrataciones as $contratacion) {
                $person = getPerson($contratacion->id_empleado);
                $He50 = getHorasExtras($contratacion->id_empleado,0,$inicio,$fin,true,50);
                $He100 = getHorasExtras($contratacion->id_empleado,0,$inicio,$fin,true,100);
                $comisiones = getComisiones($contratacion->id_empleado,$inicio,$fin,0,false,true);
                $arrBonosFijos = getBonosFijos($contratacion->id_contrataciones, false,0,$fin);
                $consumos        = /*getConsumos($contratacion->id_empleado,0,false,$fin,true)*/0.00;
                $dataAnticipos   = getAnticipos($contratacion->id_empleado,0,false,$fin,true);
                $anticipos       = $dataAnticipos['montoAnticipos'];
                $otrosDescuentos = getOtrosDescuentos($contratacion->id_empleado,0,false,$fin,false,true);
                $arrPrestamos    = getPrestamos($contratacion->id_contrataciones,0,false,$fin);
                $decimoTercero= 0.00;
                $decimoCuarto= 0.00;
                $aportePersonalIESS = 0.00;
                $aportePatronalIESS = 0.00;
                $salario = getSalarioEmpleado($contratacion->id_empleado,$request->fecha_nomina);
                $detalleContratacion = getDetalleContrato($contratacion->id_empleado)->first();
                $fondoReserva = 0.00;

                if($contratacion->tipo_contratacion->relacion_dependencia){

                    if($contratacion->decimo_tercero){//Mensualizado
                        $decimoTercero = getDecimoTercero($salario,($He50+$He100),$contratacion->id_empleado,$fin);
                    }else{
                        if($fechaActual >= Carbon::now()->format('Y-12-01') && $fechaActual <= Carbon::now()->format('Y-12-31'))
                            $decimoTercero = getDecimoTerceroAnual($contratacion->id_empleado);
                    }
                    if($contratacion->decimo_cuarto){//Mensualizado
                        $decimoCuarto  = getDecimoCuarto();
                    }else{
                        if($fechaActual >= Carbon::now()->format('Y-08-01') && $fechaActual <= Carbon::now()->format('Y-08-15') )
                            $decimoCuarto = getDecimoCuartoAnual($contratacion->id_empleado);
                    }

                    if(Carbon::parse($request->fecha_nomina)->diffInYears($detalleContratacion->fecha_expedicion_contrato) >= getConfiguracionEmpresa()->anno_calculo_fondo_reserva){

                        if($detalleContratacion->fondo_reserva){//Mensualizado

                            $fondoReserva = getFondoReserva($salario,($He50+$He100));

                        }else{

                            $fondoReserva = 0;//getFondoReservaAnual($contratacion->id_empleado,true);

                        }

                    }

                    $aportePersonalIESS = getAportePersonal($salario,($He50+$He100),$contratacion->id_contrataciones,$fin);
                    $aportePatronalIESS = getAportePatronal($salario,($He50+$He100),$contratacion->id_contrataciones,$fin);

                }

                    $ingresos = $salario + ($He50+$He100) + $comisiones + $decimoTercero + $decimoCuarto + $fondoReserva + $arrBonosFijos['montoBonosFijos'];
                    $egresos  = $aportePersonalIESS + $consumos + $anticipos + $otrosDescuentos['totalOtrosDescuentos'] + $arrPrestamos['montoPrestamos'];
                    $subTotal = $ingresos - $egresos;
                    $total = $subTotal;

                    $iva            = 0.00;
                    $retencionIva   = 0.00;
                    $retencionRenta = 0.00;

                    if(!$contratacion->tipo_contratacion->relacion_dependencia){
                        $iva            = $detalleContratacion->retencion_iva > 0 ? ($arrBonosFijos['montoBonosFijos'] + $salario + $comisiones+ ($He50+$He100))*($detalleContratacion->iva/100) : 0;
                        $retencionIva   = $detalleContratacion->retencion_iva > 0 ? $iva*($detalleContratacion->retencion_iva/100) : 0;
                        $retencionRenta = $detalleContratacion->retencion_renta > 0 ? ($arrBonosFijos['montoBonosFijos'] +$salario + $comisiones + ($He50+$He100))*($detalleContratacion->retencion_renta/100) : 0;
                        $total          = $subTotal + $iva - $retencionIva - $retencionRenta;
                    }

                    $arrDataInformeNomina[] =[
                        'empleado' =>  $person->first_name." ".$person->last_name,
                        'identificacion' => $person->identification->id_value,
                        'cargo' =>Cargo::where('id_cargo', $detalleContratacion->id_cargo)->first()->nombre,
                        'sueldo' =>$salario,
                        'H_E_50' => $He50,
                        'H_E_100' => $He100,
                        'comsiones' => $comisiones,
                        'Bonos'=>$arrBonosFijos['montoBonosFijos'],
                        'iva' => $iva,
                        '10mo_3ero' => $decimoTercero,
                        '10mo_4to' => $decimoCuarto,
                        'fondo_reserva' => $fondoReserva,
                        'aporte_patronal' => $aportePatronalIESS,
                        'anticipos' => $anticipos,
                        'consumos' => $consumos,
                        'descuento'=> $otrosDescuentos['totalOtrosDescuentos'],
                        'retencion_iva' => $retencionIva,
                        'retencion_renta' => $retencionRenta,
                        'aporte_personal' => $aportePersonalIESS,
                        'prestamos'=>  $arrPrestamos['montoPrestamos'],
                        'total' => $total
                    ];

            }
        }

        return view('layouts.views.nomina.partials.tabla_informe_nomina',[
            'fechaNomina' => $request->fecha_nomina,
            'arrDataInformeNomina' => $arrDataInformeNomina
        ]);
    }

    public function storeNominaLineal($data){

        $x=0;
        $success=false;
        foreach($data as $nomina){
            $inicio = Carbon::parse($nomina['fecha'])->format('Y-m-01');
            $fin = Carbon::parse($nomina['fecha'])->endOfMonth()->toDateString();
            $He50 = getHorasExtras($nomina['id_empleado'],0,$inicio,$fin,true,50,3);
            $He100 = getHorasExtras($nomina['id_empleado'],0,$inicio,$fin,true,100,3);
            $objNominasPasadas = new NominasPasadas;
            $objNominasPasadas->id_nomina = $nomina['id_nomina'];
            $objNominasPasadas->fecha_nomina = $nomina['fecha'];
            $objNominasPasadas->nombre = $nomina['nombre_empleado'];
            $objNominasPasadas->identificacion = $nomina['identificacion'];
            $objNominasPasadas->cargo = $nomina['cargo'];
            $objNominasPasadas->sueldo = $nomina['salario'];
            $objNominasPasadas->he_50 = $He50;
            $objNominasPasadas->he_100 = $He100;
            $objNominasPasadas->comisiones = $nomina['comisiones'];
            $objNominasPasadas->bonos = $nomina['monto_bonos_fijos'];
            $objNominasPasadas->iva = $nomina['iva'];
            $objNominasPasadas->decimo_3ero = $nomina['decimo_tercero'] == "N/A" ? 0 : $nomina['decimo_tercero'];
            $objNominasPasadas->decimo_4to = $nomina['decimo_cuarto'] == "N/A" ? 0 : $nomina['decimo_cuarto'];
            $objNominasPasadas->fondo_reserva = $nomina['fondo_reserva'] == "N/A" ? 0 : $nomina['fondo_reserva'];
            $objNominasPasadas->apt_patronal = $nomina['aporte_patronal_IEES'] == "N/A" ? 0 : $nomina['aporte_patronal_IEES'];
            $objNominasPasadas->apt_personal = $nomina['aporte_personal_IESS'] == "N/A" ? 0 : $nomina['aporte_personal_IESS'];
            $objNominasPasadas->anticipos = $nomina['anticipos'];
            $objNominasPasadas->consumos = $nomina['consumos'];
            $objNominasPasadas->prestamos = $nomina['monto_prestamos'];
            $objNominasPasadas->descuentos = $nomina['monto_descuentos'];
            $objNominasPasadas->ret_iva = $nomina['retencionIva'];
            $objNominasPasadas->ret_renta = $nomina['retencionRenta'];
            $objNominasPasadas->total = $nomina['total'];
            if($objNominasPasadas->save()) $x++;
        }
        if($x==count($data))
            $success=true;

        return $success;
    }

    public function generaFacturaHonorarios($data){

        $conexion = getConnection(0);
        DB::connection($conexion)->beginTransaction();
        //DB::beginTransaction();

        try{

            //INICIO FACTURA DE NÓMINA
            $partyEmpresa= Party::join('party_role as pr','pr.role_type_id','pr.role_type_id')
                            ->join('role_type as rt','pr.role_type_id','pr.role_type_id')
                            ->where([
                                ['pr.role_type_id' , 'INTERNAL_ORGANIZATIO'],
                                ['rt.role_type_id' , 'INTERNAL_ORGANIZATIO']
                            ])->first();
            $store = ProductStore::where('type_store','MATRIZ')->first();
            $person = getPerson($data['contrataciones']->id_empleado);

            if($data['contrataciones']->iva==0){
                $idIva= 10000;
            }elseif($data['contrataciones']->iva==14){
                $idIva= 10022;
            }else{
                $idIva = 10021;
            }

            $arrInvoices= [['honorarios' => $data['honorarios'],'iva'=> $data['iva']]];

            $const= env('MAXIMO_NOTA_VENTA');

            if($data['honorarios'] > $const && $data['contrataciones']->tipo_documento == 'NOTA_VENTA'){

                $val = $data['honorarios'];
                $arrInvoices=[];
                $x=0;

                while($val > $const){

                    if($x==0)
                        $arrInvoices[]= ['honorarios'=>$const, 'iva'=> ($const * ($data['contrataciones']->iva/100))];

                    $val = $val-$const;

                    $honorarios= $val >= $const ? $const : $val;

                    $arrInvoices[]= ['honorarios'=> $honorarios, 'iva'=> ($honorarios * ($data['contrataciones']->iva/100))];

                    $x++;

                }

            }

            $invoicesGeneradas=[];
            $letras =['A','B','C','D','E','F','G','H','I','J','K','L'];

            foreach($arrInvoices as $x => $arrI){

                $nItemInvoice= 0;
                $seqInvoice= PartyAcctgPreference::where('party_id',$partyEmpresa->party_id)->first();

                $invoice= new Invoice;
                $invoice->invoice_id = 'FA'.($seqInvoice->last_invoice_number+1);
                $invoice->invoice_number =  $data['id_nomina'].$letras[$x];
                $invoice->invoice_type_id = $data['contrataciones']->tipo_documento == 'NOTA_VENTA' ? 'NV_HONORARIOS' : 'INVOICE_HONORARIOS';
                $invoice->party_id_from = $data['contrataciones']->id_empleado;
                $invoice->party_id = $partyEmpresa->party_id;
                $invoice->product_store_id= $store->product_store_id;
                $invoice->status_id = 'INVOICE_IN_PROCESS';
                $invoice->currency_uom_id='USD';
                $invoice->due_date = $data['date']->toDateTimeString();
                $invoice->invoice_date = $data['date']->toDateTimeString();
                $invoice->description = $data['descripcion_invoice_item'];
                $invoice->last_updated_stamp = now()->toDayDateTimeString();
                $invoice->last_updated_tx_stamp = now()->toDayDateTimeString();
                $invoice->created_stamp = now()->toDayDateTimeString();
                $invoice->created_tx_stamp = now()->toDayDateTimeString();

                if($invoice->save()){

                    $dataAcctg =[
                        'acctg_trans_type_id' => $invoice->invoice_type_id,
                        'gl_fiscal_type_id' => 'ACTUAL',
                        'is_posted' => 'Y',
                        'party_id' => $invoice->party_id_from,
                        'invoice_id' => $invoice->invoice_id,
                        'created_by_user_login' => session('dataUsuario')['id_usuario_log'],
                        'last_modified_by_user_login' => session('dataUsuario')['id_usuario_log'],
                        'role_type_id' => 'EMPLOYEE',
                        'description' => 'Honorarios profesionales '. $person->first_name .' '.$person->last_name
                    ];
                    $dataAcctg['debitos']=[];
                    $dataAcctg['creditos']=[];
                    //GUARDA ITEM QUE SUMA (SUMATORIA DE TODOS LOS INGRESOS)
                    $nItemInvoice++;
                    $inoviceItem = new InvoiceItem;
                    $inoviceItem->invoice_id = $invoice->invoice_id;
                    $inoviceItem->invoice_item_seq_id =str_pad(($nItemInvoice),5,'0',STR_PAD_LEFT);
                    $inoviceItem->invoice_item_type_id = 'GASTO_HON_PROF';
                    $inoviceItem->quantity= 1.000000;
                    $inoviceItem->amount= $arrI['honorarios'];
                    $inoviceItem->taxable_flag='Y';
                    $inoviceItem->description = $data['descripcion_invoice_item'];
                    $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                    $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();

                    if($inoviceItem->save()){

                        // ASIENTO HONORARIOS //
                        $glAccountDebito = glAccountMapInvoice($inoviceItem->invoice_item_type_id); //GASTO HONORARIOS PROFESIONALES

                        if(
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->gl_account_id) || $glAccountDebito->gl_account_id=='')) &&
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->default_gl_account_id) || $glAccountDebito->default_gl_account_id==''))
                        ){
                            throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                        }

                        $dataAcctg['debitos'][]= [ //GASTO HONORARIOS PROFESIONALES
                            'organization_party_id' => $partyEmpresa->party_id,
                            'gl_account_id' => isset($glAccountDebito->gl_account_id) ? $glAccountDebito->gl_account_id : $glAccountDebito->default_gl_account_id,
                            'amount' => $arrI['honorarios']
                        ];

                        /* $res = crearAcctgTrans($dataAcctg);

                        if(!$res['success']){
                            throw new \Exception('No se pudo crear el asiento contable del item '.$inoviceItem->invoice_item_type_id.' de la factura '. $res['msg']);
                        } */
                        //unset($dataAcctg['debitos']);
                        // FIN ASIENTO HONORARIOS //

                        if($data['contrataciones']->iva > 0 && $data['iva'] && $invoice->invoice_type_id != 'NOTA_VENTA'){

                            $nItemInvoice++;
                            $inoviceItem = new InvoiceItem;
                            $inoviceItem->invoice_id = $invoice->invoice_id;
                            $inoviceItem->invoice_item_seq_id = str_pad($nItemInvoice,5,'0',STR_PAD_LEFT);
                            $inoviceItem->invoice_item_type_id = 'ITM_SALES_TAX';
                            $inoviceItem->parent_invoice_item_seq_id ='00001';
                            $inoviceItem->quantity= 1.000000;
                            $inoviceItem->amount= $arrI['iva'];
                            $inoviceItem->tax_authority_rate_seq_id = $idIva;
                            $inoviceItem->tax_auth_geo_id ='ECU';
                            $inoviceItem->tax_auth_party_id='SRI';
                            $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                            $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                            $inoviceItem->created_stamp = now()->toDayDateTimeString();
                            $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();

                            if($inoviceItem->save()){

                                // ASIENTO IVA EN COMPRAS //
                                $glAccountDebito = glAccountMapInvoice($inoviceItem->invoice_item_type_id); //GASTO IVA EN COMPRAS

                                if(
                                    (!isset($glAccountDebito) || (!isset($glAccountDebito->gl_account_id) || $glAccountDebito->gl_account_id=='')) &&
                                    (!isset($glAccountDebito) || (!isset($glAccountDebito->default_gl_account_id) || $glAccountDebito->default_gl_account_id==''))
                                ){
                                    throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                                }

                               // $dataAcctg['description'] ='Iva en compras '. $person->first_name .' '.$person->last_name;

                                $dataAcctg['debitos'][]= [ //IVA EN COMPRAS
                                    'organization_party_id' => $partyEmpresa->party_id,
                                    'gl_account_id' => isset($glAccountDebito->gl_account_id) ? $glAccountDebito->gl_account_id : $glAccountDebito->default_gl_account_id,
                                    'amount' => $arrI['iva']
                                ];

                                /* $res = crearAcctgTrans($dataAcctg);

                                if(!$res['success']){
                                    throw new \Exception('No se pudo crear el asiento contable del item '.$inoviceItem->invoice_item_type_id.' de la factura '. $res['msg']);
                                }
                                unset($dataAcctg['debitos']); */
                                // FIN ASIENTO IVA EN COMPRAS //

                            }

                        }

                        // ASIENTO CUENTA POR PAGAR PROVEEDORES //
                        $glAccountCredito = glAccountMapPayment('VENDOR_PAYMENT'); //GASTO CUENTA POR PAGAR PROVEEDORES

                        if(
                            (!isset($glAccountCredito) || (!isset($glAccountCredito->gl_account_id) || $glAccountCredito->gl_account_id=='')) &&
                            (!isset($glAccountCredito) || (!isset($glAccountCredito->default_gl_account_id) || $glAccountCredito->default_gl_account_id==''))
                        ){
                            throw new \Exception('<b>No existe una cuenta contable mapeada con VENDOR_PAYMENT </b> <br />');
                        }

                       // $dataAcctg['description'] ='Cuenta por pagar proveedores '. $person->first_name .' '.$person->last_name;

                        $dataAcctg['creditos'][]= [
                            'organization_party_id' => $partyEmpresa->party_id,
                            'gl_account_id' => isset($glAccountCredito->gl_account_id) ? $glAccountCredito->gl_account_id : $glAccountCredito->default_gl_account_id,
                            'amount' => $arrI['honorarios'] + (($data['contrataciones']->iva > 0 && $data['iva'] && $data['contrataciones']->tipo_documento != 'NOTA_VENTA') ? $arrI['iva'] : 0 )
                        ];

                        /* $res = crearAcctgTrans($dataAcctg);

                        if(!$res['success']){
                            throw new \Exception('No se pudo crear el asiento contable VENDOR_PAYMENT '. $res['msg']);
                        }
                        unset($dataAcctg['creditos']); */
                        // FIN ASIENTO CUENTA POR PAGAR PROVEEDORES //
                    }

                    if(count($data['anticipos'])){

                        foreach($data['anticipos'] as $dataAnticipo){

                            $pago = ReferenciaPago::where([
                                ['id_registro',$dataAnticipo->id_anticipo],
                                ['tipo', 'anticipos'],
                                ['aplicado',false]
                            ])->select('payment_id')->first();

                            if(isset($pago)){

                                $resPayment = paymentApplication([
                                    'paymentId' => $pago->payment_id,
                                    'facturaId' => $invoice->invoice_id,
                                    'amount_applied' => $dataAnticipo->cantidad
                                ]);

                                if(!$resPayment['success'])
                                    throw new Exception('No se pudo aplicar el pago de la factura '.$invoice->invoice_id.' '.$resPayment['msg']);

                                ReferenciaPago::where([
                                    ['id_registro',$dataAnticipo->id_anticipo],
                                    ['tipo', 'anticipos'],
                                    ['aplicado',false]
                                ])->update(['aplicado'=>true]);

                            }else{

                                throw new \Exception('No existe un pago efectuado para el anticipo '.$dataAnticipo->id_anticipo);

                            }

                        }

                    }

                    $invoicesGeneradas[]= 'FA'.($seqInvoice->last_invoice_number+1);

                    $updateInv= Invoice::find($invoice->invoice_id);
                    $updateInv->sub_total_imp1 = $data['contrataciones']->iva > 0 ? $arrI['honorarios'] : 0; //iva 12
                    $updateInv->sub_total_imp2 = $data['contrataciones']->iva == 0 ? $arrI['honorarios'] : 0; //iva 0
                    $updateInv->total_iva = $data['contrataciones']->iva > 0 ? $arrI['iva'] : 0;
                    $updateInv->save();
                }

                PartyAcctgPreference::where('party_id',$partyEmpresa->party_id)->update(['last_invoice_number' => $seqInvoice->last_invoice_number+1]);

                $res = crearAcctgTrans($dataAcctg);

                if(!$res['success'])
                    throw new \Exception('No se pudo crear los asientos contables de la factura'. $res['msg']);

            }

            //FIN FACTURA DE NÓMINA;

            //INICIO DE NCI_NOMINA (NOTA CREDITO INTERNA) PARA DESCUENTOS

            if(count($data['prestamos']) || count($data['otrosDescuentos'])){

                $seqInvoice = PartyAcctgPreference::where('party_id',$partyEmpresa->party_id)->first();

                $partyAutorizacionSri = PartyAutorizacionSri::where('party_id',$partyEmpresa->party_id)
                                        ->where('tipo_documento','NCIN')
                                        ->where('estado','ACTIVO')
                                        ->first();

                $newSequencial = isset($partyAutorizacionSri)
                                    ? $partyAutorizacionSri->secuencial_actual+1
                                    : '';

                $nItemInvoice=0;
                $invoice= new Invoice;
                $invoice->invoice_id = 'FA'.($seqInvoice->last_invoice_number+1);
                $invoice->invoice_type_id = 'NCI_NOMINA';
                $invoice->party_id_from = $data['contrataciones']->id_empleado;
                $invoice->party_id = $partyEmpresa->party_id;
                $invoice->product_store_id= $store->product_store_id;
                $invoice->status_id = 'INVOICE_READY';
                if($newSequencial != ''){
                    $invoice->invoice_number = $newSequencial;
                    $invoice->codigo_establecimiento = $partyAutorizacionSri->cod_estab;
                    $invoice->codigo_punto_emision = $partyAutorizacionSri->cod_pto_emision;
                }
                $invoice->currency_uom_id='USD';
                $invoice->due_date = $data['date']->toDateTimeString();
                $invoice->invoice_date = $data['date']->toDateTimeString();
                $invoice->description = 'NOTA DE CRÉDITO INTERNA GENERADA DESDE EL MÓDULO DE NÓMINA';
                $invoice->last_updated_stamp = now()->toDateTimeString();
                $invoice->last_updated_tx_stamp = now()->toDateTimeString();
                $invoice->created_stamp = now()->toDateTimeString();
                $invoice->created_tx_stamp = now()->toDateTimeString();

                if($invoice->save()){

                    //$invoicesGeneradas[]=$invoice->invoice_id;

                    $dataAcctg =[
                        'acctg_trans_type_id' => 'NCI_NOMINA',
                        'gl_fiscal_type_id' => 'ACTUAL',
                        'is_posted' => 'Y',
                        'party_id' => $invoice->party_id_from,
                        'invoice_id' => $invoice->invoice_id,
                        'created_by_user_login' => session('dataUsuario')['id_usuario_log'],
                        'last_modified_by_user_login' => session('dataUsuario')['id_usuario_log'],
                        'role_type_id' => 'EMPLOYEE',
                        'description' => 'Descuento de pago de servicios profesionales '. $person->first_name .' '.$person->last_name
                    ];

                    if($newSequencial!=''){
                        PartyAutorizacionSri::where([
                            ['party_id',$partyEmpresa->party_id],
                            ['tipo_documento','NCIN'],
                            ['estado','ACTIVO']
                        ])->update(['secuencial_actual' => $newSequencial]);
                    }

                    foreach($data['prestamos'] as $dataPrestamo){
                        $nItemInvoice++;
                        $inoviceItem = new InvoiceItem;
                        $inoviceItem->invoice_id = $invoice->invoice_id;
                        $inoviceItem->invoice_item_seq_id = str_pad($nItemInvoice,5,'0',STR_PAD_LEFT);
                        $inoviceItem->invoice_item_type_id = $dataPrestamo->invoice_item_type_id;
                        $inoviceItem->quantity= 1.000000;
                        $inoviceItem->amount= $dataPrestamo->cuota;
                        $inoviceItem->description = $dataPrestamo->nombre;
                        $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                        $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                        $inoviceItem->created_stamp = now()->toDayDateTimeString();
                        $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();
                        if($inoviceItem->save()){

                            // ASIENTO DESCUENTO PRESTAMO //
                            $glAccountCredito = glAccountMapInvoice($inoviceItem->invoice_item_type_id);

                            if(
                                (!isset($glAccountCredito) || (!isset($glAccountCredito->gl_account_id) || $glAccountCredito->gl_account_id=='')) &&
                                (!isset($glAccountCredito) || (!isset($glAccountCredito->default_gl_account_id) || $glAccountCredito->default_gl_account_id==''))
                            ){
                                throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                            }

                            $dataAcctg['creditos'][]= [ //DESCUENTO PRESTAMO
                                'organization_party_id' => $partyEmpresa->party_id,
                                'gl_account_id' => isset($glAccountCredito->gl_account_id) ? $glAccountCredito->gl_account_id : $glAccountCredito->default_gl_account_id,
                                'amount' =>  $dataPrestamo->cuota
                            ];

                            $glAccountDebito = glAccountNCIHonorarios($partyEmpresa->party_id);

                            $dataAcctg['debitos'][]= [
                                'organization_party_id' => $partyEmpresa->party_id,
                                'gl_account_id' => $glAccountDebito->gl_account_id,
                                'amount' =>  $dataPrestamo->cuota
                            ];

                            /* $res = crearAcctgTrans($dataAcctg);

                            if(!$res['success']){
                                throw new \Exception('No se pudo crear el asiento contable del item '.$inoviceItem->invoice_item_type_id.' de la factura '. $res['msg']);
                            }
                            unset($dataAcctg['creditos'],$dataAcctg['debitos']); */
                            //FIN ASIENTO DESCUENTO PRESTAMO

                        }
                    }

                    foreach($data['otrosDescuentos'] as $dataOtroDescuento){
                        $nItemInvoice++;
                        $inoviceItem = new InvoiceItem;
                        $inoviceItem->invoice_id = $invoice->invoice_id;
                        $inoviceItem->invoice_item_seq_id = str_pad($nItemInvoice,5,'0',STR_PAD_LEFT);
                        $inoviceItem->invoice_item_type_id = $dataOtroDescuento->invoice_item_type_id;
                        $inoviceItem->quantity= 1.000000;
                        $inoviceItem->amount= $dataOtroDescuento->cantidad;
                        $inoviceItem->description =  $dataOtroDescuento->descripcion;
                        $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                        $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                        $inoviceItem->created_stamp = now()->toDayDateTimeString();
                        $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();

                        if($inoviceItem->save()){

                            // ASIENTO DESCUENTO OTROS DESCUENTOS //
                            $glAccountCredito = glAccountMapInvoice($inoviceItem->invoice_item_type_id);

                            if(
                                (!isset($glAccountCredito) || (!isset($glAccountCredito->gl_account_id) || $glAccountCredito->gl_account_id=='')) &&
                                (!isset($glAccountCredito) || (!isset($glAccountCredito->default_gl_account_id) || $glAccountCredito->default_gl_account_id==''))
                            ){
                                throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                            }

                            //$dataAcctg['description'] = $dataOtroDescuento->descripcion.' '. $person->first_name .' '.$person->last_name;

                            $dataAcctg['creditos'][]= [
                                'organization_party_id' => $partyEmpresa->party_id,
                                'gl_account_id' => isset($glAccountCredito->gl_account_id) ? $glAccountCredito->gl_account_id : $glAccountCredito->default_gl_account_id,
                                'amount' =>  $dataOtroDescuento->cantidad
                            ];

                            $glAccountDebito = glAccountNCIHonorarios($partyEmpresa->party_id);

                            $dataAcctg['debitos'][]= [
                                'organization_party_id' => $partyEmpresa->party_id,
                                'gl_account_id' => $glAccountDebito->gl_account_id,
                                'amount' =>  $dataOtroDescuento->cantidad
                            ];

                            //$invoice->invoice_type_id

                            /* $res = crearAcctgTrans($dataAcctg);

                            if(!$res['success']){
                                throw new \Exception('No se pudo crear el asiento contable del item '.$inoviceItem->invoice_item_type_id.' de la factura '. $res['msg']);
                            }
                            unset($dataAcctg['creditos'],$dataAcctg['debitos']); */
                            // FIN ASIENTO DESCUENTO OTROS DESCUENTOS //

                        }
                    }

                    $res = crearAcctgTrans($dataAcctg);

                    if(!$res['success']){
                        throw new \Exception('No se pudo crear el asiento contable del item '.$inoviceItem->invoice_item_type_id.' de la factura '. $res['msg']);
                    }

                }

                PartyAcctgPreference::where('party_id',$partyEmpresa->party_id)->update(['last_invoice_number' => $seqInvoice->last_invoice_number+1]);
            }

            //asientosFacturas($invoice->invoice_id);
            //FIN DE NCI_NOMINA (NOTA CREDITO INTERNA)

            DB::connection($conexion)->commit();
            //DB::commit();

            return [
                'invoiceId' => $invoicesGeneradas,
                'success'=> true,
                'msg'=> 'Factura de honorarios creada con éxito'
            ];

        }catch(\Exception $e){
            //DB::rollback();
            DB::connection($conexion)->rollBack();
            return ['success'=> false, 'msg'=> $e->getMessage().' '.$e->getFile().' '. $e->getLine()];
        }

    }

    public function generaFacturaRelacionDependencia($data){

        //INICIO FACTURA DE NÓMINA;
        $conexion = getConnection(0);
        DB::connection($conexion)->beginTransaction();

        try{

            $empresa = cuentaEmpresa();
            $store = ProductStore::where('type_store','MATRIZ')->first();
            $seqInvoice = PartyAcctgPreference::where('party_id',$empresa->party_id)->first();
            $person = getPerson($data['contrataciones']->id_empleado);
            $invoiceId = $seqInvoice->last_invoice_number+1;
            PartyAcctgPreference::where('party_id',$empresa->party_id)->update(['last_invoice_number' => $invoiceId]);

            $nItemInvoice=0;
            $invoice= new Invoice;
            $invoice->invoice_id = 'FA'.$invoiceId;
            $invoice->invoice_type_id = 'PAYROL_INVOICE';
            $invoice->party_id_from = $data['contrataciones']->id_empleado;
            $invoice->party_id = $empresa->party_id;
            $invoice->product_store_id=$store->product_store_id;
            $invoice->status_id = 'INVOICE_READY';
            $invoice->currency_uom_id='USD';
            $invoice->due_date =  $data['date']->toDateTimeString();
            $invoice->invoice_date = $data['date']->toDateTimeString();
            $invoice->description = $data['descripcion_invoice'];  //'FACTURA DE ROL DE PAGO GENERADA DESDE EL MÓDULO DE NÓMINA';
            $invoice->sub_total_imp1 = 0;
            $invoice->sub_total_imp2 = $data['total'];
            $invoice->total_iva = 0;
            $invoice->last_updated_stamp = now()->toDayDateTimeString();
            $invoice->last_updated_tx_stamp = now()->toDayDateTimeString();
            $invoice->created_stamp = now()->toDayDateTimeString();
            $invoice->created_tx_stamp = now()->toDayDateTimeString();


            $dataAcctg =[
                'acctg_trans_type_id' => 'PAYROL_INVOICE',
                'gl_fiscal_type_id' => 'ACTUAL',
                'is_posted' => 'Y',
                'party_id' => $invoice->party_id_from,
                'invoice_id' => $invoice->invoice_id,
                'created_by_user_login' => session('dataUsuario')['id_usuario_log'],
                'last_modified_by_user_login' => session('dataUsuario')['id_usuario_log'],
                'role_type_id' => 'EMPLOYEE',
                'description' => 'Pagon nómina '. $person->first_name .' '.$person->last_name
            ];

            if($invoice->save()){

                if($data['base']>0){
                    //GUARDA ITEM SALARIO BASE
                    $nItemInvoice++;
                    $inoviceItem = new InvoiceItem;
                    $inoviceItem->invoice_id = $invoice->invoice_id;
                    $inoviceItem->invoice_item_seq_id =str_pad(($nItemInvoice),5,'0',STR_PAD_LEFT);
                    $inoviceItem->invoice_item_type_id = 'SALARIO_PR';
                    $inoviceItem->quantity= 1.000000;
                    $inoviceItem->amount= $data['base'];
                    $inoviceItem->description = $data['descripcion_invoice_item'];
                    $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                    $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();

                    $inoviceItem->save();

                    // ASIENTO SUELDO BASE //
                    $glAccountDebito = glAccountMapInvoice($inoviceItem->invoice_item_type_id); //SUELDO BÁSICO

                    if(
                        (!isset($glAccountDebito) || (!isset($glAccountDebito->gl_account_id) || $glAccountDebito->gl_account_id=='')) &&
                        (!isset($glAccountDebito) || (!isset($glAccountDebito->default_gl_account_id) || $glAccountDebito->default_gl_account_id==''))
                    ){
                        throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                    }

                    $dataAcctg['debitos'][]= [ //SUELDO BASE
                        'organization_party_id' => $empresa->party_id,
                        'gl_account_id' => isset($glAccountDebito->gl_account_id) ? $glAccountDebito->gl_account_id : $glAccountDebito->default_gl_account_id,
                        'amount' => $data['base']
                    ];

                    /* $res = crearAcctgTrans($dataAcctg);

                    if(!$res['success'])
                        throw new \Exception('No se pudo crear el asiento contable del item  SALARIO BASE de la factura '. $res['msg']);

                    unset($dataAcctg['debitos']); */
                    // FIN ASIENTO SUELDO BASE //
                }

                //GUARDA ITEM HORAS EXTRAS
                if($data['horasExtras'] > 0){
                    $nItemInvoice++;
                    $inoviceItem = new InvoiceItem;
                    $inoviceItem->invoice_id = $invoice->invoice_id;
                    $inoviceItem->invoice_item_seq_id = str_pad(($nItemInvoice),5,'0',STR_PAD_LEFT);
                    $inoviceItem->invoice_item_type_id = 'HE_PR';
                    $inoviceItem->quantity = 1.000000;
                    $inoviceItem->amount = $data['horasExtras'];
                    $inoviceItem->description = $data['descripcion_hora_extra'];
                    $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                    $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();
                    if($inoviceItem->save()){
                        // ASIENTO HORAS EXTRAS //
                        $glAccountDebito = glAccountMapInvoice($inoviceItem->invoice_item_type_id); //HORAS EXTRAORDINARIAS

                        if(
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->gl_account_id) || $glAccountDebito->gl_account_id=='')) &&
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->default_gl_account_id) || $glAccountDebito->default_gl_account_id==''))
                        ){
                            throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                        }

                        $dataAcctg['description'] = 'Horas extras '. $person->first_name .' '.$person->last_name;

                        $dataAcctg['debitos'][]= [ //HORAS EXTRAORDINARIAS
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => isset($glAccountDebito->gl_account_id) ? $glAccountDebito->gl_account_id : $glAccountDebito->default_gl_account_id,
                            'amount' => $data['horasExtras']
                        ];

                        /* $res = crearAcctgTrans($dataAcctg);

                        if(!$res['success']){
                            throw new \Exception('No se pudo crear el asiento contable del item  SALARIO BASE de la factura '. $res['msg']);
                        }
                        unset($dataAcctg['debitos']); */
                        // FIN ASIENTO HORAS EXTRAS //
                    }
                }

                //GUARDA ITEM BONOS
                if($data['bonos'] > 0){
                    $nItemInvoice++;
                    $inoviceItem = new InvoiceItem;
                    $inoviceItem->invoice_id = $invoice->invoice_id;
                    $inoviceItem->invoice_item_seq_id = str_pad(($nItemInvoice),5,'0',STR_PAD_LEFT);
                    $inoviceItem->invoice_item_type_id = 'BONOS_PR';
                    $inoviceItem->quantity = 1.000000;
                    $inoviceItem->amount = $data['bonos'];
                    $inoviceItem->description = $data['descripcion_bono'];
                    $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                    $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();
                    if($inoviceItem->save()){
                        // ASIENTO BONOS //
                        $glAccountDebito = glAccountMapInvoice($inoviceItem->invoice_item_type_id); //HORAS EXTRAORDINARIAS

                        if(
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->gl_account_id) || $glAccountDebito->gl_account_id=='')) &&
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->default_gl_account_id) || $glAccountDebito->default_gl_account_id==''))
                        ){
                            throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                        }

                        $dataAcctg['description'] = 'Bono '. $person->first_name .' '.$person->last_name;

                        $dataAcctg['debitos'][]= [ //BONOS
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => isset($glAccountDebito->gl_account_id) ? $glAccountDebito->gl_account_id : $glAccountDebito->default_gl_account_id,
                            'amount' => $data['bonos']
                        ];

                        /* $res = crearAcctgTrans($dataAcctg);

                        if(!$res['success']){
                            throw new \Exception('No se pudo crear el asiento contable del item  SALARIO BASE de la factura '. $res['msg']);
                        }
                        unset($dataAcctg['debitos']); */
                        // FIN ASIENTO BONOS //
                    }
                }

                //GUARDA ITEM COMSIONES
                if($data['comisiones'] > 0){
                    $nItemInvoice++;
                    $inoviceItem = new InvoiceItem;
                    $inoviceItem->invoice_id = $invoice->invoice_id;
                    $inoviceItem->invoice_item_seq_id = str_pad(($nItemInvoice),5,'0',STR_PAD_LEFT);
                    $inoviceItem->invoice_item_type_id = 'COMISIONES_PR';
                    $inoviceItem->quantity = 1.000000;
                    $inoviceItem->amount = $data['comisiones'];
                    $inoviceItem->description = $data['descripcion_comision'];
                    $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                    $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();
                    if($inoviceItem->save()){
                        // ASIENTO COMISIONES //
                        $glAccountDebito = glAccountMapInvoice($inoviceItem->invoice_item_type_id); // TRANSPORTE GENERAL

                        if(
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->gl_account_id) || $glAccountDebito->gl_account_id=='')) &&
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->default_gl_account_id) || $glAccountDebito->default_gl_account_id==''))
                        ){
                            throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                        }

                        $dataAcctg['description'] = 'Comisiones '. $person->first_name .' '.$person->last_name;

                        $dataAcctg['debitos'][]= [ //COMISIONES
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => isset($glAccountDebito->gl_account_id) ? $glAccountDebito->gl_account_id : $glAccountDebito->default_gl_account_id,
                            'amount' =>  $data['comisiones']
                        ];

                        /* $res = crearAcctgTrans($dataAcctg);

                        if(!$res['success']){
                            throw new \Exception('No se pudo crear el asiento contable del item '.$inoviceItem->invoice_item_type_id.' de la factura '. $res['msg']);
                        }
                        unset($dataAcctg['debitos']); */
                        // FIN ASIENTO COMISIONES //
                    }

                }

                //GUARDA ITEM FONDO RESERVA
                if($data['contrataciones']->fondo_reserva && $data['fondoReserva'] > 0){ //MENSUALIZADO
                    $nItemInvoice++;
                    $inoviceItem = new InvoiceItem;
                    $inoviceItem->invoice_id = $invoice->invoice_id;
                    $inoviceItem->invoice_item_seq_id = str_pad(($nItemInvoice),5,'0',STR_PAD_LEFT);
                    $inoviceItem->invoice_item_type_id = 'FND_RESERVA_PR';
                    $inoviceItem->quantity = 1.000000;
                    $inoviceItem->amount = $data['fondoReserva'];
                    $inoviceItem->description = $data['descripcion_fondo_reserva'];
                    $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                    $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();
                    if($inoviceItem->save()){
                        // ASIENTO FONDO RESERVA //
                        $glAccountDebito = glAccountMapInvoice($inoviceItem->invoice_item_type_id); // FONDO DE RESERVA

                        if(
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->gl_account_id) || $glAccountDebito->gl_account_id=='')) &&
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->default_gl_account_id) || $glAccountDebito->default_gl_account_id==''))
                        ){
                            throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                        }

                        $dataAcctg['description'] = 'Fondo de reserva '. $person->first_name .' '.$person->last_name;

                        $dataAcctg['debitos'][]= [ //FONDO DE RESERVA
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => isset($glAccountDebito->gl_account_id) ? $glAccountDebito->gl_account_id : $glAccountDebito->default_gl_account_id,
                            'amount' =>  $data['fondoReserva']
                        ];

                        /* $res = crearAcctgTrans($dataAcctg);

                        if(!$res['success']){
                            throw new \Exception('No se pudo crear el asiento contable del item '.$inoviceItem->invoice_item_type_id.' de la factura '. $res['msg']);
                        }
                        unset($dataAcctg['debitos']); */
                        // FIN ASIENTO FONDO RESERVA //
                    }
                }

                //GUARDA ITEM DECIMO TERCERO
                if($data['decimoTercero'] > 0){ //MENSUALIZADO
                    $nItemInvoice++;
                    $inoviceItem = new InvoiceItem;
                    $inoviceItem->invoice_id = $invoice->invoice_id;
                    $inoviceItem->invoice_item_seq_id = str_pad(($nItemInvoice),5,'0',STR_PAD_LEFT);
                    $inoviceItem->invoice_item_type_id = 'DEC_3ERO_PR';
                    $inoviceItem->quantity = 1.000000;
                    $inoviceItem->amount = $data['decimoTercero'];
                    $inoviceItem->description = $data['descripcion_dcmo_3er'];
                    $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                    $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();
                    if($inoviceItem->save()){

                        // ASIENTO DECIMO TERCERO //
                        $glAccountDebito = glAccountMapInvoice($inoviceItem->invoice_item_type_id); // DECIMO TERCERO

                        if(
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->gl_account_id) || $glAccountDebito->gl_account_id=='')) &&
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->default_gl_account_id) || $glAccountDebito->default_gl_account_id==''))
                        ){
                            throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                        }

                        $dataAcctg['description'] = 'Décimo tercer sueldo '. $person->first_name .' '.$person->last_name;

                        $dataAcctg['debitos'][]= [ //DECIMO TERCERO
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => isset($glAccountDebito->gl_account_id) ? $glAccountDebito->gl_account_id : $glAccountDebito->default_gl_account_id,
                            'amount' =>  $data['decimoTercero']
                        ];

                        /* $res = crearAcctgTrans($dataAcctg);

                        if(!$res['success']){
                            throw new \Exception('No se pudo crear el asiento contable del item '.$inoviceItem->invoice_item_type_id.' de la factura '. $res['msg']);
                        }
                        unset($dataAcctg['debitos']); */
                        // FIN ASIENTO DECIMO TERCERO //

                    }
                }

                //GUARDA ITEM DECIMO CUARTO
                if($data['decimoCuarto'] > 0){ //MENSUALIZADO
                    $nItemInvoice++;
                    $inoviceItem = new InvoiceItem;
                    $inoviceItem->invoice_id = $invoice->invoice_id;
                    $inoviceItem->invoice_item_seq_id = str_pad(($nItemInvoice),5,'0',STR_PAD_LEFT);
                    $inoviceItem->invoice_item_type_id = 'DEC_4TO_PR';
                    $inoviceItem->quantity = 1.000000;
                    $inoviceItem->amount = $data['decimoCuarto'];
                    $inoviceItem->description = $data['descripcion_dcmo_4to'];
                    $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                    $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();
                    if($inoviceItem->save()){
                        // ASIENTO DECIMO CUARTO //
                        $glAccountDebito = glAccountMapInvoice($inoviceItem->invoice_item_type_id); // DECIMO CUARTO

                        if(
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->gl_account_id) || $glAccountDebito->gl_account_id=='')) &&
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->default_gl_account_id) || $glAccountDebito->default_gl_account_id==''))
                        ){
                            throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                        }

                        $dataAcctg['description'] = 'Décimo cuarto sueldo '. $person->first_name .' '.$person->last_name;

                        $dataAcctg['debitos'][]= [ //DECIMO CUARTO
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => isset($glAccountDebito->gl_account_id) ? $glAccountDebito->gl_account_id : $glAccountDebito->default_gl_account_id,
                            'amount' =>  $data['decimoCuarto']
                        ];

                        /* $res = crearAcctgTrans($dataAcctg);

                        if(!$res['success']){
                            throw new \Exception('No se pudo crear el asiento contable del item '.$inoviceItem->invoice_item_type_id.' de la factura '. $res['msg']);
                        }
                        unset($dataAcctg['debitos']); */
                        // FIN ASIENTO DECIMO CUARTO //
                    }
                }

                //GUARDA ITEM VACACIONES
                if($data['vacaciones'] > 0){ //LIQUIDACIÓN DE VACACIONES
                    $nItemInvoice++;
                    $inoviceItem = new InvoiceItem;
                    $inoviceItem->invoice_id = $invoice->invoice_id;
                    $inoviceItem->invoice_item_seq_id = str_pad(($nItemInvoice),5,'0',STR_PAD_LEFT);
                    $inoviceItem->invoice_item_type_id = 'VACACIONES_PR';
                    $inoviceItem->quantity = 1.000000;
                    $inoviceItem->amount = $data['vacaciones'];
                    $inoviceItem->description = 'LIQUIDACIÓN VACACIONES DEL ROL DE PAGO';
                    $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                    $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();
                    if($inoviceItem->save()){

                        // ASIENTO VACACIONES //
                        $glAccountDebito = glAccountMapInvoice($inoviceItem->invoice_item_type_id); // VACACIONES

                        if(
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->gl_account_id) || $glAccountDebito->gl_account_id=='')) &&
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->default_gl_account_id) || $glAccountDebito->default_gl_account_id==''))
                        ){
                            throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                        }

                        $dataAcctg['description'] = 'Vacaciones '. $person->first_name .' '.$person->last_name;

                        $dataAcctg['debitos'][]= [ //VACACIONES
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => isset($glAccountDebito->gl_account_id) ? $glAccountDebito->gl_account_id : $glAccountDebito->default_gl_account_id,
                            'amount' =>  $data['decimoCuarto']
                        ];

                        /* $res = crearAcctgTrans($dataAcctg);

                        if(!$res['success']){
                            throw new \Exception('No se pudo crear el asiento contable del item '.$inoviceItem->invoice_item_type_id.' de la factura '. $res['msg']);
                        }
                        unset($dataAcctg['debitos']); */
                        // FIN ASIENTO VACACIONES //

                    }
                }

                //GUARDA ITEM DE PRESTAMOS
                foreach($data['prestamos'] as $dataPrestamo){

                    $nItemInvoice++;
                    $inoviceItem = new InvoiceItem;
                    $inoviceItem->invoice_id = $invoice->invoice_id;
                    $inoviceItem->invoice_item_seq_id = str_pad($nItemInvoice,5,'0',STR_PAD_LEFT);
                    $inoviceItem->invoice_item_type_id = $dataPrestamo->invoice_item_type_id;
                    $inoviceItem->quantity= 1.000000;
                    $inoviceItem->amount= $dataPrestamo->cuota;
                    $inoviceItem->description = $dataPrestamo->nombre;
                    $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                    $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();

                    if($inoviceItem->save()){

                        // ASIENTO DESCUENTO PRESTAMO //
                        $glAccountCredito = glAccountMapInvoice($inoviceItem->invoice_item_type_id);

                        if(
                            (!isset($glAccountCredito) || (!isset($glAccountCredito->gl_account_id) || $glAccountCredito->gl_account_id=='')) &&
                            (!isset($glAccountCredito) || (!isset($glAccountCredito->default_gl_account_id) || $glAccountCredito->default_gl_account_id==''))
                        ){
                            throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                        }

                        $dataAcctg['description'] = 'Descuento cuota prestamo ';

                        $dataAcctg['creditos'][]= [ //DESCUENTO PRESTAMO
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => isset($glAccountCredito->gl_account_id) ? $glAccountCredito->gl_account_id : $glAccountCredito->default_gl_account_id,
                            'amount' =>  $dataPrestamo->cuota
                        ];

                        /* $res = crearAcctgTrans($dataAcctg);

                        if(!$res['success'])
                            throw new \Exception('No se pudo crear el asiento contable del item '.$inoviceItem->invoice_item_type_id.' de la factura '. $res['msg']);

                        unset($dataAcctg['creditos']); */
                        // FIN ASIENTO DESCUENTO PRESTAMO //

                        //INOVICE QUE DESCUENTA LA CUOTA DE UN PRESTAMO APLICANDO EL INVOICE AL PAYMENT
                        /*$invoiceDescPrestamo = new Invoice;
                        $invoiceDescPrestamo->invoice_id = 'FA'.($invoiceId+1);
                        $invoiceDescPrestamo->invoice_type_id = 'CUOTA_PRESTAMO';
                        $invoiceDescPrestamo->party_id_from = $data['contrataciones']->id_empleado;
                        $invoiceDescPrestamo->party_id = $empresa->party_id;
                        $invoiceDescPrestamo->product_store_id = $store->product_store_id;
                        $invoiceDescPrestamo->status_id = 'INVOICE_READY';
                        $invoiceDescPrestamo->currency_uom_id = 'USD';
                        $invoiceDescPrestamo->due_date = now()->toDayDateTimeString();
                        $invoiceDescPrestamo->invoice_date = now()->toDayDateTimeString();
                        $invoiceDescPrestamo->description = 'Pago de cuota de prestamo institucional';
                        $invoiceDescPrestamo->last_updated_stamp = now()->toDayDateTimeString();
                        $invoiceDescPrestamo->last_updated_tx_stamp = now()->toDayDateTimeString();
                        $invoiceDescPrestamo->created_stamp = now()->toDayDateTimeString();
                        $invoiceDescPrestamo->created_tx_stamp = now()->toDayDateTimeString();

                        if($invoiceDescPrestamo->save()){

                            $inoviceItemDescPago = new InvoiceItem;
                            $inoviceItemDescPago->invoice_id = $invoiceDescPrestamo->invoice_id;
                            $inoviceItemDescPago->invoice_item_seq_id = '00001';
                            $inoviceItemDescPago->invoice_item_type_id = 'REG_ANTIC_ROL_PAGO';
                            $inoviceItemDescPago->quantity= 1.000000;
                            $inoviceItemDescPago->amount= $dataPrestamo->cuota;
                            $inoviceItemDescPago->description = 'Cuota prestamo ID'. $dataPrestamo->id_prestamo.' '.$person->first_name .' '.$person->last_name;
                            $inoviceItemDescPago->last_updated_stamp = now()->toDayDateTimeString();
                            $inoviceItemDescPago->last_updated_tx_stamp = now()->toDayDateTimeString();
                            $inoviceItemDescPago->created_stamp = now()->toDayDateTimeString();
                            $inoviceItemDescPago->created_tx_stamp = now()->toDayDateTimeString();

                            if($inoviceItemDescPago->save()){

                                PartyAcctgPreference::where('party_id',$empresa->party_id)->update(['last_invoice_number' => $invoiceId+1]);

                                $pago = ReferenciaPago::where([
                                    ['tipo','prestamo'],
                                    ['id_registro',$dataPrestamo->id_prestamo]
                                ])->first();

                                if(!isset($pago))
                                    throw new \Exception('<b>No existe el pago efectuado para descontar la cuota del prestamo '.$dataPrestamo->id_prestamo.' perteneciente a '.$person->first_name .' '.$person->last_name.'</b>');

                                if($pago->aplicado)
                                    throw new \Exception('<b>El pago del prestamo '.$dataPrestamo->id_prestamo.' perteneciente a '.$person->first_name .' '.$person->last_name.' ya fue aplicado</b>');

                                $resPayment = paymentApplication([
                                    'paymentId' => $pago->payment_id,
                                    'facturaId' => $invoiceDescPrestamo->invoice_id,
                                    'amount_applied' => $dataPrestamo->cuota
                                ]);

                                if(!$resPayment['success'])
                                    throw new Exception('No se pudo aplicar el pago de la factura '.$invoiceDescPrestamo->invoice_id.' '.$resPayment['msg']);

                                if($dataPrestamo->pagado){
                                    ReferenciaPago::where([
                                        ['tipo','prestamo'],
                                        ['id_registro',$dataPrestamo->id_prestamo],
                                        ['aplicado',false]
                                    ])->update(['aplicado'=> true]);
                                }
                            }

                        }*/

                    }

                }

                //GUARDA ITEM DE ANTICIPOS
                foreach($data['anticipos'] as $dataAnticipo){

                    $nItemInvoice++;
                    $inoviceItem = new InvoiceItem;
                    $inoviceItem->invoice_id = $invoice->invoice_id;
                    $inoviceItem->invoice_item_seq_id = str_pad($nItemInvoice,5,'0',STR_PAD_LEFT);
                    $inoviceItem->invoice_item_type_id = $dataAnticipo->invoice_item_type_id;
                    $inoviceItem->quantity= 1.000000;
                    $inoviceItem->amount=$dataAnticipo->cantidad;
                    $inoviceItem->description = $data['descripcion_ancticipo'];
                    $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                    $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();

                    if($inoviceItem->save()){



                        // ASIENTO ANTICICPO //
                        $glAccountCredito = glAccountMapInvoice($inoviceItem->invoice_item_type_id); // ANTICICPO

                        if(
                            (!isset($glAccountCredito) || (!isset($glAccountCredito->gl_account_id) || $glAccountCredito->gl_account_id=='')) &&
                            (!isset($glAccountCredito) || (!isset($glAccountCredito->default_gl_account_id) || $glAccountCredito->default_gl_account_id==''))
                        ){
                            throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                        }

                        $dataAcctg['description'] = 'Débito de anticipo '. $person->first_name .' '.$person->last_name;

                        $dataAcctg['creditos'][]= [ //ANTICIPO
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => isset($glAccountCredito->gl_account_id) ? $glAccountCredito->gl_account_id : $glAccountCredito->default_gl_account_id,
                            'amount' =>  $dataAnticipo->cantidad
                        ];

                        /* $res = crearAcctgTrans($dataAcctg);

                        if(!$res['success']){
                            throw new \Exception('No se pudo crear el asiento contable del item '.$inoviceItem->invoice_item_type_id.' de la factura '. $res['msg']);
                        }
                        unset($dataAcctg['creditos']); */
                        // FIN ASIENTO ANTICICPO //

                    }
                }

                //GUARDA ITEM DE DESCUENTOS
                foreach($data['otrosDescuentos'] as $dataOtroDescuento){
                    $nItemInvoice++;
                    $inoviceItem = new InvoiceItem;
                    $inoviceItem->invoice_id = $invoice->invoice_id;
                    $inoviceItem->invoice_item_seq_id = str_pad($nItemInvoice,5,'0',STR_PAD_LEFT);
                    $inoviceItem->invoice_item_type_id = $dataOtroDescuento->invoice_item_type_id;
                    $inoviceItem->quantity= 1.000000;
                    $inoviceItem->amount= $dataOtroDescuento->cantidad;
                    $inoviceItem->description =  $dataOtroDescuento->descripcion;
                    $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                    $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();
                    if($inoviceItem->save()){

                        // ASIENTO DESCUENTO //
                        $glAccountCredito = glAccountMapInvoice($inoviceItem->invoice_item_type_id); // DESCUENTO

                        if(
                            (!isset($glAccountCredito) || (!isset($glAccountCredito->gl_account_id) || $glAccountCredito->gl_account_id=='')) &&
                            (!isset($glAccountCredito) || (!isset($glAccountCredito->default_gl_account_id) || $glAccountCredito->default_gl_account_id==''))
                        ){
                            throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                        }

                        $dataAcctg['description'] = 'Descuento '.$dataOtroDescuento->descripcion.' '.$person->first_name.' '.$person->last_name;

                        $dataAcctg['creditos'][]= [ //DESCUENTO
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => isset($glAccountCredito->gl_account_id) ? $glAccountCredito->gl_account_id : $glAccountCredito->default_gl_account_id,
                            'amount' =>  $dataOtroDescuento->cantidad
                        ];

                        /* $res = crearAcctgTrans($dataAcctg);

                        if(!$res['success']){
                            throw new \Exception('No se pudo crear el asiento contable del item '.$inoviceItem->invoice_item_type_id.' de la factura '. $res['msg']);
                        }
                        unset($dataAcctg['creditos']); */
                        // FIN ASIENTO DESCUENTO //

                    }
                }

                //GUARDA APORTE PERSONAL
                if($data['aportePersonalIESS'] > 0){
                    $nItemInvoice++;
                    $inoviceItem = new InvoiceItem;
                    $inoviceItem->invoice_id = $invoice->invoice_id;
                    $inoviceItem->invoice_item_seq_id = str_pad($nItemInvoice,5,'0',STR_PAD_LEFT);
                    $inoviceItem->invoice_item_type_id = 'APT_PERSONAL_PR';
                    $inoviceItem->quantity= 1.000000;
                    $inoviceItem->amount= $data['aportePersonalIESS'];
                    $inoviceItem->description =  'Aporte personal IESS';
                    $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                    $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();
                    if($inoviceItem->save()){

                        // ASIENTO APORTE PERSONAL //
                        $glAccountCredito = glAccountMapInvoice($inoviceItem->invoice_item_type_id); // APORTE PERSONAL

                        if(
                            (!isset($glAccountCredito) || (!isset($glAccountCredito->gl_account_id) || $glAccountCredito->gl_account_id=='')) &&
                            (!isset($glAccountCredito) || (!isset($glAccountCredito->default_gl_account_id) || $glAccountCredito->default_gl_account_id==''))
                        ){
                            throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                        }

                        $dataAcctg['description'] = 'Aporte personal IESS '.$person->first_name.' '.$person->last_name;

                        $dataAcctg['creditos'][]= [ //APORTE PERSONAL
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => isset($glAccountCredito->gl_account_id) ? $glAccountCredito->gl_account_id : $glAccountCredito->default_gl_account_id,
                            'amount' =>  $data['aportePersonalIESS']
                        ];

                        /* $res = crearAcctgTrans($dataAcctg);

                        if(!$res['success']){
                            throw new \Exception('No se pudo crear el asiento contable del item '.$inoviceItem->invoice_item_type_id.' de la factura '. $res['msg']);
                        }
                        unset($dataAcctg['creditos']); */
                        // FIN ASIENTO APORTE PERSONAL //

                    }
                }

                //GUARDA BONO 25%
                if(isset($data['bono_25']) && $data['bono_25'] > 0){
                    $nItemInvoice++;
                    $inoviceItem = new InvoiceItem;
                    $inoviceItem->invoice_id = $invoice->invoice_id;
                    $inoviceItem->invoice_item_seq_id = str_pad($nItemInvoice,5,'0',STR_PAD_LEFT);
                    $inoviceItem->invoice_item_type_id = 'BONO_25';
                    $inoviceItem->quantity= 1.000000;
                    $inoviceItem->amount= $data['bono_25'];
                    $inoviceItem->description = 'Bono 25%';
                    $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                    $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();
                    if($inoviceItem->save()){

                        // ASIENTO BONO 25% //
                        $glAccountCredito = glAccountMapInvoice($inoviceItem->invoice_item_type_id); // BONO 25%

                        if(
                            (!isset($glAccountCredito) || (!isset($glAccountCredito->gl_account_id) || $glAccountCredito->gl_account_id=='')) &&
                            (!isset($glAccountCredito) || (!isset($glAccountCredito->default_gl_account_id) || $glAccountCredito->default_gl_account_id==''))
                        ){
                            throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                        }

                        $dataAcctg['description'] = 'Bono 25% '.$person->first_name.' '.$person->last_name;

                        $dataAcctg['creditos'][]= [ //BONO 25%
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => isset($glAccountCredito->gl_account_id) ? $glAccountCredito->gl_account_id : $glAccountCredito->default_gl_account_id,
                            'amount' =>  $data['bono_25']
                        ];

                        /* $res = crearAcctgTrans($dataAcctg);

                        if(!$res['success']){
                            throw new \Exception('No se pudo crear el asiento contable del item '.$inoviceItem->invoice_item_type_id.' de la factura '. $res['msg']);
                        }
                        unset($dataAcctg['creditos']); */
                        // FIN ASIENTO APORTE PERSONAL //

                    }
                }

                //asientosFacturas($invoice->invoice_id);

                // ASIENTO SUELDO POR PAGAR //
                $glAccountCredito = glAccountMapPayment('PAYROL_PAYMENT'); // SUELDO POR PAGAR

                if(
                    (!isset($glAccountCredito) || (!isset($glAccountCredito->gl_account_id) || $glAccountCredito->gl_account_id=='')) &&
                    (!isset($glAccountCredito) || (!isset($glAccountCredito->default_gl_account_id) || $glAccountCredito->default_gl_account_id==''))
                ){
                    throw new \Exception('<b>No existe una cuenta contable mapeada para PAYROL_PAYMENT </b> <br />');
                }

                $dataAcctg['description'] = 'SUELDO POR PAGAR '.$person->first_name.' '.$person->last_name;

                $dataAcctg['creditos'][]= [ //SUELDO POR PAGAR
                    'organization_party_id' => $empresa->party_id,
                    'gl_account_id' => isset($glAccountCredito->gl_account_id) ? $glAccountCredito->gl_account_id : $glAccountCredito->default_gl_account_id,
                    'amount' =>  $data['total']
                ];

                /* $res = crearAcctgTrans($dataAcctg);

                if(!$res['success']){
                    throw new \Exception('No se pudo crear el asiento contable SUELDOS POR PAGAR '. $res['msg']);
                }
                unset($dataAcctg['creditos']); */
                // FIN ASIENTO SUELDO POR PAGAR //

                $res = crearAcctgTrans($dataAcctg);

                if(!$res['success'])
                    throw new \Exception('No se pudo crear los asientos contables de la factura '. $res['msg']);
            }

            //FIN FACTURA DE NÓMINA;
            DB::connection($conexion)->commit();
            return [
                'invoiceId' => 'FA'.$invoiceId,
                'success'=> true,
                'msg'=> 'Factura creada con de rol de pago con éxito'
            ];

        }catch(\Exception $e){

            DB::connection($conexion)->rollBack();
            return ['success'=> false, 'msg'=> $e->getMessage().' '.$e->getFile().' '. $e->getLine()];
        }

    }

    public function fileCashManagment(Request $request)
    {
        $dataFile='';

        $cuentaEmpresa = cuentaEmpresa();

        if($request->tipo== '1'){

            $dataContrataciones = contratacionesCashManagement(array_column($request->empleados,'id_empleado'))->get();

            foreach($dataContrataciones as $dataContratacion){

                $nomina =  Nomina::where([
                    ['fecha_nomina', Carbon::parse($request->fecha)->endOfMonth()],
                    ['id_empleado', $dataContratacion->party_id],
                    ['liquidacion',false]
                ])->select('total','persona')->first();

                if(isset($nomina))
                    $dataFile.= "PA\t".str_pad($cuentaEmpresa->account_number,20)."\t1\t1\t".str_pad($dataContratacion->id_value,13)."\tUSD\t".str_pad(str_replace(['.',','],'',number_format($nomina->total,2)),13,'0',STR_PAD_LEFT)."\tCTA\t". str_pad($dataContratacion->enum_code2,13)."\t".$dataContratacion->tipo_cuenta."\t".str_pad($dataContratacion->account_number,20)."\t".$dataContratacion->codigo_identificacion."\t".str_pad($dataContratacion->id_value,14)."\t".str_pad("PAGO NÓMINA ".(getMes(intval(Carbon::parse($request->fecha)->format('m')))." del ". Carbon::parse($request->fecha)->format('Y').' '.$dataContratacion->empleado),40)."\t\n";
            }

        }elseif($request->tipo== '2'){

            $dataLiquidaciones = Nomina::where([
                ['fecha_nomina','>=',Carbon::parse($request->fecha)->format('Y-m-01')],
                ['fecha_nomina','<=',Carbon::parse($request->fecha)->endOfMonth()->format('Y-m-d')],
                ['liquidacion',true]
            ])->get();

            foreach($dataLiquidaciones as $liquidacion){

                $dataContratacion = contratacionesCashManagement($liquidacion->id_empleado)->first();

                $dataFile.= "PA\t".str_pad($cuentaEmpresa->account_number,20)."\t1\t1\t".str_pad($dataContratacion->id_value,13)."\tUSD\t".str_pad(str_replace(['.',','],'',number_format($liquidacion->total,2)),13,'0',STR_PAD_LEFT)."\tCTA\t". str_pad($dataContratacion->enum_code2,13)."\t".$dataContratacion->tipo_cuenta."\t".str_pad($dataContratacion->account_number,20)."\t".$dataContratacion->codigo_identificacion."\t".str_pad($dataContratacion->id_value,14)."\t".str_pad("PAGO LIQUIDACIÓN ". $dataContratacion->empleado,40)."\t\n";

            }

        }else{

            $dataAlcances = Nomina::where([
                ['fecha_nomina','>=', Carbon::parse($request->fecha)->format('Y-m-01')],
                ['fecha_nomina','<=', Carbon::parse($request->fecha)->endOfMonth()->format('Y-m-d')]
            ])->join('alcance_nomina as an','nomina.id_nomina','an.id_nomina')->get();

            foreach($dataAlcances as $dataAlcance){

                $dataContratacion = contratacionesCashManagement($dataAlcance->id_empleado)->first();

                $total = $dataAlcance->sueldo+$dataAlcance->hora_extra+$dataAlcance->comision+$dataAlcance->bono+$dataAlcance->dcmo_3ro+$dataAlcance->dcmo_4to+$dataAlcance->fondo_reserva;

                $dataFile.= "PA\t".str_pad($cuentaEmpresa->account_number,20)."\t1\t1\t".str_pad($dataContratacion->id_value,13)."\tUSD\t".str_pad(str_replace(['.',','],'',number_format($total,2)),13,'0',STR_PAD_LEFT)."\tCTA\t". str_pad($dataContratacion->enum_code2,13)."\t".$dataContratacion->tipo_cuenta."\t".str_pad($dataContratacion->account_number,20)."\t".$dataContratacion->codigo_identificacion."\t".str_pad($dataContratacion->id_value,14)."\t".str_pad("PAGO ALCANCE NÓMINA ".(getMes(intval(Carbon::parse($dataAlcance->fecha_nomina)->format('m')))." del ". Carbon::parse($dataAlcance->fecha_nomina)->format('Y').' '.$dataContratacion->empleado),40)."\t\n";

            }

        }

        return base64_encode($dataFile);

    }

    public function crearAlcanceNomina(Request $request)
    {
        return view('layouts.views.nomina.partials.form_alcance_nomina',[
            'empleado' => $request->empleado,
            'relacionDependencia' => $request->relacion_dependencia,
            'idNomina' => $request->id_nomina
        ]);
    }

    public function storeAlcanceNomina(Request $request)
    {
        try{

            DB::transaction(function() use($request) {

                $an = new AlcanceNomina;
                $an->id_nomina = $request->id_nomina;
                $an->sueldo = isset($request->sueldo) ? $request->sueldo : 0;
                $an->hora_extra = isset($request->hora_extra) ? $request->hora_extra :0;
                $an->comision = isset($request->comision) ? $request->comision :0;
                $an->bono = isset($request->bono) ? $request->bono : 0;

                isset($request->dcmo_3ro) && $an->dcmo_3ro = $request->dcmo_3ro;

                isset($request->dcmo_4to) &&  $an->dcmo_4to = $request->dcmo_4to;

                isset($request->fondo_reserva) && $an->fondo_reserva = $request->fondo_reserva;

                $an->user_login_id = $request->user_login_id;
                $an->comentario = $request->comentario;

                $nomina = Nomina::find($request->id_nomina);

                $total = $request->sueldo +
                $request->hora_extra +
                $request->comision +
                $request->bono +
                (isset($request->dcmo_3ro) ? $request->dcmo_3ro : 0) +
                (isset($request->dcmo_4to) ? $request->dcmo_4to : 0) +
                (isset($request->fondo_reserva) ? $request->fondo_reserva : 0);

                $contratacion = Contrataciones::join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')
                ->join('tipo_contrato as tc', 'contrataciones.id_tipo_contrato','tc.id_tipo_contrato')
                ->where([
                    ['contrataciones.id_tipo_contrato_descripcion',2],
                    ['contrataciones.estado',1],
                    ['contrataciones.id_empleado', $nomina->id_empleado]
                ])->first();

                $retencionRenta = 0;
                $retencionIva = 0;
                $iva = 0;
                $aportePersonalIESS=0;
                $aportePatronalIESS=0;

                $model = (object)[
                    'id_nomina' => $request->id_nomina,
                    'fecha_nomina' => $nomina->fecha_nomina
                ];
                if($contratacion->relacion_dependencia){

                    saveDataMensualRelacionDependecia(
                        $contratacion,
                        $model,
                        isset($request->dcmo_3ro) ? $request->dcmo_3ro : 0,
                        0,
                        isset($request->fondo_reserva) ? $request->fondo_reserva : 0,
                        0,
                        isset($request->dcmo_4to) ? $request->dcmo_4to : 0
                    );

                    $aportePersonalIESS = getAportePersonal($request->sueldo,$request->hora_extra,$contratacion->id_contrataciones,'1970-01-01');
                    $aportePatronalIESS = getAportePatronal($request->sueldo,$request->hora_extra,$contratacion->id_contrataciones,'1970-01-01');

                    $an->aporte_personal = $aportePersonalIESS;
                    $an->aporte_patronal = $aportePatronalIESS;

                    $total-= $aportePersonalIESS;

                    $invoice = $this->generaFacturaRelacionDependencia([
                        'contrataciones' => $contratacion,
                        'date' => Carbon::parse($nomina->fecha_nomina),
                        'base' => $request->sueldo,
                        'horasExtras' => $request->hora_extra,
                        'bonos' => $request->bono,
                        'comisiones' => $request->comision,
                        'fondoReserva' => isset($request->fondo_reserva) ? $request->fondo_reserva : 0,
                        'decimoTercero' => isset($request->dcmo_3ro) ? $request->dcmo_3ro : 0,
                        'decimoCuarto' => isset($request->dcmo_4to) ? $request->dcmo_4to : 0,
                        'vacaciones' => 0,
                        'prestamos' => [],
                        'anticipos' => [],
                        'total' => $total,
                        'otrosDescuentos' => [],
                        'aportePersonalIESS' => $aportePersonalIESS,
                        'descripcion_invoice' => 'FACTURA DE ALCANCE DE NÓMINA',
                        'descripcion_invoice_item' => 'INGRESO ALCANCE SALARIO BASE',
                        'descripcion_hora_extra' => 'ALCANCE DE NÓMINA DE HORAS EXTRAS',
                        'descripcion_bono' => 'ALCANCE DE NÓMINA DE BONOS',
                        'descripcion_comision'=> 'ALCANCE DE NÓMINA DE COMSIONES',
                        'descripcion_fondo_reserva' => 'ALCANCE DE NÓMINA DE FONDO DE RESERVA',
                        'descripcion_dcmo_3er' => 'ALCANCE DE NÓMINA DE FONDO DE DECIMO 3er SUELDO',
                        'descripcion_dcmo_4to' => 'ALCANCE DE NÓMINA DE FONDO DE DECIMO 4to SUELDO',
                        'descripcion_ancticipo' => 'ALCANCE DE NÓMINA DE FONDO DE ANTICIPO'
                    ]);

                }else{

                    $subTotal= $total;
                    $iva = $contratacion->retencion_iva > 0 ? ($subTotal)*($contratacion->iva/100) : 0;
                    $retencionIva = $contratacion->retencion_iva > 0 ? $iva*($contratacion->retencion_iva/100) : 0;
                    $retencionRenta = $contratacion->retencion_renta > 0 ? ($subTotal)*($contratacion->retencion_renta/100) : 0;
                    $total = $subTotal + $iva - $retencionIva - $retencionRenta;

                    $an->retencion_iva = $retencionIva;
                    $an->retencion_renta = $retencionRenta;
                    $an->iva = $iva;

                    $invoice = $this->generaFacturaHonorarios([
                        'id_nomina' => $request->id_nomina,
                        'honorarios' => $total,
                        'contrataciones' => $contratacion,
                        'date' => Carbon::parse($nomina->fecha_nomina),
                        'iva' => $iva,
                        'prestamos' => [],
                        'anticipos' => [],
                        'otrosDescuentos' => [],
                        'descripcion_invoice_item' => 'INGRESOS DE NÓMINA POR MOTIVO DE ALCANCE'
                    ]);
                    $invoice['invoiceId'] = $invoice['invoiceId'][0];
                }
                $an->invoice_id = $invoice['invoiceId'];
                $an->total = $total;
                $an->save();

                $dataEmpleado = Person::where('person.party_id',$nomina->id_empleado)
                                ->join('party_identification as pi','person.party_id','pi.party_id')
                                ->join('party_identification_type as pit','pi.party_identification_type_id','pit.party_identification_type_id')
                                ->select('person.first_name','person.last_name','pit.description','pi.id_value','person.party_id')->first();

                $contratacion = Contrataciones::join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')
                ->join('tipo_contrato as tc', 'contrataciones.id_tipo_contrato','tc.id_tipo_contrato')
                ->where([
                    ['contrataciones.id_tipo_contrato_descripcion',2],
                    ['contrataciones.estado',1],
                    ['contrataciones.id_empleado', $nomina->id_empleado]
                ])->first();

                $dataRolIndividual = [
                    'nombre_empleado' => $dataEmpleado->first_name . " " . $dataEmpleado->last_name,
                    'cargo'           => getCargo($dataEmpleado->party_id)->nombre,
                    'documento'       => $dataEmpleado->description,
                    'identificacion'  => $dataEmpleado->id_value,
                    'id_empleado'     => $dataEmpleado->party_id,
                    'salario'         => isset($request->sueldo) ? number_format($request->sueldo,2) : 0,
                    'horas_extras'    => isset($request->hora_extra) ? number_format($request->hora_extra,2) : 0,
                    'comisiones'      => isset($request->comision) ? number_format($request->comision,2) : 0,
                    'bono'            => isset($request->bono) ? number_format($request->bono,2) : 0,
                    'iva'             => $iva == 0 ? 'N/A' : number_format($iva,2),
                    'retencion_renta' => $retencionRenta == 0 ? 'N/A' : number_format($retencionRenta,2),
                    'retencion_iva'   => $retencionIva == 0 ? 'N/A' : number_format($retencionIva,2),
                    'decimo_tercero'  => isset($request->dcmo_3ro) ? number_format($request->dcmo_3ro,2) : 'N/A',
                    'decimo_cuarto'   => isset($request->dcmo_4to) ? number_format($request->dcmo_4to,2) : 'N/A',
                    'aporte_personal' => $aportePersonalIESS,
                    'aporte_patronal' => $aportePatronalIESS,
                    'fondo_reserva'   => isset($request->fondo_reserva) ? number_format($request->fondo_reserva,2) : 'N/A',
                    'total'           => $total,
                    'fecha_nomina'    => Carbon::parse($nomina->fecha_nomina),
                    'comentario'     => $request->comentario
                ];

                $number = mt_rand();
                $nombreRol = "alcance-nomina".$number."(".$dataEmpleado->first_name." ". $dataEmpleado->last_name.").pdf";
                $view =  \View::make('layouts.views.nomina.recibo_nomina_alcance_pdf',compact('dataRolIndividual'))->render();
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->save(public_path('roles_pago').'/'.$nombreRol);

                $objImagenesRoles = new ImagenesRoles;
                $objImagenesRoles->id_empleado = $nomina->id_empleado;
                $objImagenesRoles->fecha_nomina = $nomina->fecha_nomina;
                $objImagenesRoles->nombre_imagen = $nombreRol;
                $objImagenesRoles->tipo = 3;
                $objImagenesRoles->save();

            });

            $msg = '<div class="alert alert-success" role="alert" style="margin: 10px">
                        Se ha generado el alcance de nómina exitosamente, ahora puede descargar
                        el archivo para cargar al Cash managment
                    </div>';
            $status = true;

        }catch(\Exception $e){

            $msg = '<div class="alert alert-danger" role="alert" style="margin: 10px">
                        Ha ocurrido un error al guardar el alcance de nómina <br /> '.$e->getMessage().' '. $e->getFile().' '.$e->getLine().'
                    </div>';
            $status = false;
        }

        return response()->json(['status'=>$status,'msg'=>$msg]);
    }

    public function formCashManagement(Request $request)
    {
       return view('layouts/views/nomina/partials/form_cash_management',[
           'tipo' => $request->tipo,
           'fecha' => $request->fecha
       ]);
    }

    public function storeReferenciaBancaria(Request $request)
    {

        $conexion = getConnection(0);
        try{

            DB::connection($conexion)->beginTransaction();
            DB::beginTransaction();
            $empresa = cuentaEmpresa();

            if($request->tipo == '1' || $request->tipo == '2'){ //NÓNIMIA || LIQUIDACION

                $nominas = Nomina::where('liquidacion', !isset($request->tipo) ? false : ($request->tipo=='2' ? true : false))
                ->where(function($q) use($request){
                    if(isset($request->empleados))
                       $q->whereIn('nomina.id_empleado',array_column($request->empleados,'id_empleado'));
                })->whereBetween('fecha_nomina',[
                    Carbon::parse($request->fecha)->format('Y-m-01'),
                    Carbon::parse($request->fecha)->endOfMonth()->format('Y-m-d')
                ])->join('contrataciones as c','nomina.id_contrataciones','c.id_contrataciones')
                    ->join('tipo_contrato as tc','c.id_tipo_contrato','tc.id_tipo_contrato')
                    ->whereNotIn('id_nomina',function($query){
                        $query->select('id_registro')->from('referencia_pago')->where('tipo','nomina');
                })->select('nomina.*','tc.relacion_dependencia')->get();

                foreach($nominas as $nomina){  // NÓMINA

                    $person = getPerson($nomina->id_empleado);

                    $facturas = explode(',',$nomina->id_factura);

                    $descripcionPago  = getMes(intval(Carbon::parse($nomina->fecha_nomina)->format('m'))).' '.Carbon::parse($nomina->fecha_nomina)->format('Y').' '.$person->first_name.' '. $person->last_name;

                    $res = crearPago([
                        'payment_type_id' => $nomina->relacion_dependencia ? 'PAYROL_PAYMENT' : 'VENDOR_PAYMENT',
                        'empresa' => $empresa,
                        'referencia' => $request->referencia,
                        'comentario' => $descripcionPago,
                        'tipo' => 'nomina',
                        'id_registro' => $nomina->id_nomina,
                        'monto' => $nomina->total,
                        'person' => $person,
                        'aplicado' =>true,
                        'fecha' => $nomina->fecha
                    ]);

                    if(!$res['success']){
                        $msgPersonal=true;
                        throw new \Exception('No se pudo generara el pago '. $res['msg']);
                    }

                    $paymentId = $res['paymentId'];

                    $dataAcctg =[
                        'acctg_trans_type_id' => 'OUTGOING_PAYMENT',
                        'gl_fiscal_type_id' => 'ACTUAL',
                        'is_posted' => 'Y',
                        'party_id' => $nomina->id_empleado,
                        'payment_id' => $paymentId,
                        'description' =>  ($request->tipo == '1' ? 'Pago de nómina ' : 'Pago de liquidación '). $descripcionPago,
                        'created_by_user_login' => session('dataUsuario')['id_usuario_log'],
                        'last_modified_by_user_login' => session('dataUsuario')['id_usuario_log'],
                        'role_type_id' => 'BILL_FROM_VENDOR'
                    ];

                    //CREAR ASIENTOS DEL PAGO
                    if($nomina->relacion_dependencia){

                        if($request->tipo=='2'){ // LIQUIDACION

                            /// 1 ASIENTO
                            $glAccountCredito = glAccountMapPayment('LIQUIDACION_HABERES');
                            $dataAcctg['creditos'][] =[
                                'organization_party_id' => $empresa->party_id,
                                'gl_account_id' => $glAccountCredito->gl_account_id,
                                'amount' => $nomina->total
                            ];
                            $res = crearAcctgTrans($dataAcctg);
                            if(!$res['success']){
                                $msgPersonal=true;
                                throw new \Exception('No se pudo crear el asiento contable '. $res['msg']);
                            }
                            ////////

                            /// 2 ASIENTO
                            //DEBITO
                            $glAccountDebito = glAccountMapPayment('LIQUIDACION_HABERES');
                            $dataAcctg['debitos'][]= [
                                'organization_party_id' => $empresa->party_id,
                                'gl_account_id' => $glAccountDebito->gl_account_id,
                                'amount' => $nomina->total
                            ];

                            //CREDITO BANCOS
                            $dataAcctg['creditos'][] =[
                                'organization_party_id' => $empresa->party_id,
                                'gl_account_id' => $empresa->post_to_gl_account_id,
                                'amount' => $nomina->total
                            ];

                            $res = crearAcctgTrans($dataAcctg);
                            if(!$res['success']){
                                $msgPersonal=true;
                                throw new \Exception('No se pudo crear el asiento contable '. $res['msg']);
                            }
                            ////


                        }else{ //NOMINA

                            // PAYROL_PAYMENT
                            $glAccountDebito = glAccountMapPayment('PAYROL_PAYMENT');
                            $dataAcctg['debitos'][]= [
                                'organization_party_id' => $empresa->party_id,
                                'gl_account_id' => $glAccountDebito->gl_account_id,
                                'amount' => $nomina->total
                            ];

                            // BANCOS
                            $dataAcctg['creditos'][] =[
                                'organization_party_id' => $empresa->party_id,
                                'gl_account_id' => $empresa->post_to_gl_account_id,
                                'amount' => $nomina->total
                            ];

                            $res = crearAcctgTrans($dataAcctg);
                            if(!$res['success']){
                                $msgPersonal=true;
                                throw new \Exception('No se pudo crear el asiento contable '. $res['msg']);
                            }
                            ////////////
                        }

                    }else{

                        $glAccountDebito = glAccountMapPayment('VENDOR_PAYMENT');

                        //CUENTAS POR PAGAR PROVEEDORES
                        $dataAcctg['debitos'][]= [
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => $glAccountDebito->gl_account_id,
                            'amount' => $nomina->total
                        ];

                        // BANCOS
                        $dataAcctg['creditos'][] =[
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => $empresa->post_to_gl_account_id,
                            'amount' => $nomina->total
                        ];

                        $res = crearAcctgTrans($dataAcctg);
                        if(!$res['success']){
                            $msgPersonal=true;
                            throw new \Exception('No se pudo crear el asiento contable '. $res['msg']);
                        }

                        //APLICAR CRUCE DE PAGO DE NOTA DE CRÉDITO INTERNA
                        $nciNomina = Invoice::whereIn('invoice_id',$facturas)->where('invoice_type_id','NCI_NOMINA')->first();

                        if(isset($nciNomina)){

                            unset($facturas[array_search($nciNomina->invoice_id,$facturas)]);

                            $itemsDescHonorario = InvoiceItem::where('invoice_id',$nciNomina->invoice_id);

                            $montoDescuento = $itemsDescHonorario->sum('amount');

                            $itemsDescuento = $itemsDescHonorario->join('invoice_item_type as iit','invoice_item.invoice_item_type_id','iit.invoice_item_type_id')
                                                ->select('iit.description')->get()->toArray();

                            $itemsDescuento = implode(', ',array_column($itemsDescuento,'description'));

                            $resPagoNc = crearPago([
                                'payment_type_id' => 'CRUCE_NC',
                                'payment_method_type_id' => 'CRUCE_NCC',
                                'empresa' => $empresa,
                                'referencia' => $request->referencia,
                                'comentario' => 'Cruce de nota de crédito interna, descuento de nómina '.$itemsDescuento.' (Honorarios profesionales) '.$person->first_name.' '.$person->last_name,
                                'monto' => $montoDescuento,
                                'person' => $person,
                                'aplicado' =>true,
                                'fecha' => $nciNomina->invoice_date
                            ]);

                            if(!$resPagoNc['success'])
                                throw new Exception('No se pudo crear el pago del cruce de la nota de crédito interna de nómina '.$nciNomina->invoice_id.' '.$resPagoNc['msg']);

                            $paymentIdNci = $resPagoNc['paymentId'];

                            $resPayment = paymentApplication([
                                'paymentId' => $paymentIdNci,
                                'facturaId' => $nciNomina->invoice_id,
                                'amount_applied' => $montoDescuento
                            ]);

                            if(!$resPayment['success'])
                                throw new Exception('No se pudo aplicar el pago de la nota de crédito '.$nciNomina->invoice_id.' '.$resPayment['msg']);

                            //Cruzar la nota de credito con la factura
                            //Hacer dos pagos uno que cancele la nota de credito y otro que disminuya la factura (No generar asientos)
                            // payment_type CRUCE_NC
                            //payment_method_type CRUCE_NCC
                            //Relacionar los dos pagos creados anteriormente en la tabla payment campo payment_ref_id

                        }

                    }

                    foreach($facturas as $x => $facturaId){

                        //2DO PAGO, APLICADO A LA FACTURA DE HONORARIOS PARA DESCONTAR LA NOTA DE CRÉDITO
                        if(isset($montoDescuento) && isset($itemsDescHonorario) && isset($itemsDescuento) && $x==0){

                            $resPagoDesc = crearPago([
                                'payment_type_id' => 'CRUCE_NC',
                                'payment_method_type_id' => 'CRUCE_NCC',
                                'empresa' => $empresa,
                                'referencia' => $request->referencia,
                                'comentario' => 'Descuento de nómina '.$itemsDescuento.' (Honorarios profesionales) '.$person->first_name.' '.$person->last_name,
                                'monto' => $montoDescuento,
                                'person' => $person,
                                'aplicado' =>true,
                                'fecha' => $nomina->fecha
                            ]);

                            if(!$resPagoDesc['success'])
                                throw new Exception('No se pudo crear el pago del cruce de la nota de crédito interna de nómina con la factura '.$facturaId.' '.$resPagoDesc['msg']);

                            $resPayment = paymentApplication([
                                'paymentId' => $resPagoDesc['paymentId'],
                                'facturaId' => $facturaId,
                                'amount_applied' => $montoDescuento
                            ]);

                            if(!$resPayment['success'])
                                throw new Exception('No se pudo aplicar el pago de la factura '.$facturaId.' '.$resPayment['msg']);

                            if(isset($paymentIdNci)){
                                Payment::where('payment_id',$paymentIdNci)->update(['payment_ref_id' => $resPagoDesc['paymentId']]);
                                Payment::where('payment_id',$resPagoDesc['paymentId'])->update(['payment_ref_id' => $paymentIdNci]);
                            }

                        }

                        $invoice = Invoice::where('invoice_id',$facturaId)->select('invoice_type_id')->first();

                        if($invoice->invoice_type_id == 'NV_HONORARIOS'){

                            if(isset($montoDescuento) && ($montoDescuento+$nomina->total) > env('MAXIMO_NOTA_VENTA')){

                                $nomina->total = env('MAXIMO_NOTA_VENTA')-$montoDescuento;
                                unset($montoDescuento);

                            }else{

                                $nomina->total = InvoiceItem::where('invoice_id',$facturaId)->sum('amount');

                            }

                        }

                        //APLICACION DEL PAGO DE LA NÓMINA
                        $resPayment = paymentApplication([
                            'paymentId' => $paymentId,
                            'facturaId' => $facturaId,
                            'amount_applied' => $nomina->total
                        ]);


                        if(!$resPayment['success'])
                            throw new Exception('No se pudo aplicar el pago de la factura '.$facturaId.' '.$resPayment['msg']);

                    }

                }

            }else if($request->tipo == '3'){ // ALCANCES DE NÓMINA

                $AlcancesNomina = AlcanceNomina::join('nomina as n','alcance_nnomina.id_nomina','c.id_nomina')
                ->join('contrataciones as c','nomina.id_contrataciones','c.id_contrataciones')
                ->join('tipo_contrato as tc','c.id_tipo_contrato','tc.id_tipo_contrato')
                ->where('n.liquidacion',false)
                ->whereNotIn('id_nomina',function($query){
                    $query->select('id_registro')->from('referencia_pago')->where('tipo','nomina');
                })->select(
                    'alcance_nomina.*','tc.relacion_dependencia','n.id_empleado','n.fecha_nomina',
                    DB::raw('select sum(sueldo+hora_extra+comision+bono+dcmo_3ro+dcmo_4to+fondo_reserva) as monto from alcance_nomina where id_nomina = n.id_nomina')
                )->get();

                foreach($AlcancesNomina as $alcanceNomina){

                    $person = getPerson($alcanceNomina->id_empleado);
                    $descripcionPago  = 'Pago de alcance de nómina '.getMes(intval(Carbon::parse($alcanceNomina->fecha_nomina)->format('m'))).' '.Carbon::parse($alcanceNomina->fecha_nomina)->format('Y').' '.$person->first_name.' '. $person->last_name;

                    $res = crearPago([
                        'payment_type_id' => $alcanceNomina->relacion_dependencia ? 'PAYROL_PAYMENT' : 'VENDOR_PAYMENT',
                        'empresa' => $empresa,
                        'referencia' => $request->referencia,
                        'comentario' => $descripcionPago,
                        'tipo' => 'alcance_nomina',
                        'id_registro' => $alcanceNomina->id_alcance_nomina,
                        'monto' => $alcanceNomina->total,
                        'person' => $person,
                        'fecha' => $AlcancesNomina->fecha->nomina
                    ]);

                    $paymentId = $res['paymentId'];

                    $dataAcctg =[
                        'acctg_trans_type_id' => 'OUTGOING_PAYMENT',
                        'gl_fiscal_type_id' => 'ACTUAL',
                        'is_posted' => 'Y',
                        'party_id' => $alcanceNomina->id_empleado,
                        'payment_id' => $paymentId,
                        'description' => $descripcionPago,
                        'created_by_user_login' => session('dataUsuario')['id_usuario_log'],
                        'last_modified_by_user_login' => session('dataUsuario')['id_usuario_log'],
                        'role_type_id' => 'BILL_FROM_VENDOR'
                    ];

                    if(!$res['success']){
                        $msgPersonal=true;
                        throw new \Exception('No se pudo crear el asiento contable '. $res['msg']);
                    }

                    if($alcanceNomina->relacion_dependencia){

                        if($alcanceNomina->sueldo){

                            $glAccountDebito = glAccountMapPayment('SULEDO_BASICO');

                            //SUELDO BASICO
                            $dataAcctg['debitos'][]= [
                                'organization_party_id' => $empresa->party_id,
                                'gl_account_id' => $glAccountDebito->gl_account_id,
                                'amount' => $alcanceNomina->total
                            ];


                        }

                    }else{

                        $glAccountDebito = glAccountMapPayment('HONORARIOS');

                        //HONORARIOS
                        $dataAcctg['debitos'][]= [
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => $glAccountDebito->gl_account_id,
                            'amount' => $alcanceNomina->total
                        ];

                        // BANCOS
                        $dataAcctg['creditos'][] =[
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => $empresa->post_to_gl_account_id,
                            'amount' => $alcanceNomina->total
                        ];

                        $res = crearAcctgTrans($dataAcctg);
                        if(!$res['success']){
                            $msgPersonal=true;
                            throw new \Exception('No se pudo crear el asiento contable '. $res['msg']);
                        }

                    }

                }

            }

            $msg = '<div class="alert alert-success" role="alert" style="margin: 10px">
                        Se han generado los pagos
                    </div>';
            $status = true;
            DB::connection($conexion)->commit();
            DB::commit();

        }catch(\Exception $e){

            DB::connection($conexion)->rollBack();
            DB::rollBack();
            $msg = '<div class="alert alert-danger" role="alert" style="margin: 10px">
                        Ha ocurrido un error al guardar generar los pagos <br /> '
                        .$e->getMessage().' '.(!isset($msgPersonal) ? $e->getFile().' '. $e->getLine() : '').'
                    </div>';
            $status = false;

        }

        return response()->json(['status'=>$status,'msg'=>$msg]);

    }

    public function formCashManagementDecimos(Request $request)
    {
        $contrataciones = contratacionesDecimosAnualizado($request->tipo);

        return view('layouts/views/nomina/partials/form_cash_management_decimos',[
            'tipo' => $request->tipo,
            'contrataciones' => $contrataciones
        ]);
    }

    public function fileCashManagmentDecimos(Request $request)
    {
        $dataFile='';

        $cuentaEmpresa = cuentaEmpresa();

        $contrataciones = contratacionesDecimosAnualizado($request->tipo);

        foreach($contrataciones as $contratacion){

            $dataContratacion = contratacionesCashManagement($contratacion->id_empleado)->first();

            if($request->tipo =='DECIMO_4TO'){
                $total = getDecimoCuartoAnual($contratacion->id_empleado);
            }else{
                $total = getDecimoTerceroAnual($contratacion->id_empleado,false,false);
            }

            $dataFile.= "PA\t".str_pad($cuentaEmpresa->account_number,20)."\t1\t1\t".str_pad($dataContratacion->id_value,13)."\tUSD\t".str_pad(str_replace(['.',','],'',number_format($total,2)),13,'0',STR_PAD_LEFT)."\tCTA\t". str_pad($dataContratacion->enum_code2,13)."\t".$dataContratacion->tipo_cuenta."\t".str_pad($dataContratacion->account_number,20)."\t".$dataContratacion->codigo_identificacion."\t".str_pad($dataContratacion->id_value,14)."\t".str_pad("PAGO ".($request->tipo =='DECIMO_4TO' ? 'DECIMO 4TO SUELDO':'DECIMO 3ER SUELDO').' '.strtoupper($dataContratacion->empleado),40)."\t\n";

        }

        return base64_encode($dataFile);
    }

    public function storeReferenciaBancariaDecimos(Request $request)
    {
        $conexion = getConnection(0);

        DB::connection($conexion)->beginTransaction();
        DB::beginTransaction();

        try{

            $contrataciones = contratacionesDecimosAnualizado($request->tipo);

            $empresa = cuentaEmpresa();

            $store = ProductStore::where('type_store','MATRIZ')->first();

            foreach($contrataciones as $contratacion){

                $seqInvoice = PartyAcctgPreference::where('party_id',$empresa->party_id)->first();
                $person = getPerson($contratacion->id_empleado);
                $invoiceId = $seqInvoice->last_invoice_number+1;
                PartyAcctgPreference::where('party_id',$empresa->party_id)->update(['last_invoice_number' => $invoiceId]);

                if($request->tipo == 'DECIMO_4TO'){
                    $total = getDecimoCuartoAnual($contratacion->id_empleado,true);
                }else{
                    $total = getDecimoTerceroAnual($contratacion->id_empleado,false,true);
                }

                $pagoDecimos = new PagoDecimos;
                $pagoDecimos->id_empleado = $contratacion->id_empleado;
                $pagoDecimos->tipo = $request->tipo;
                $pagoDecimos->monto = $total;
                $pagoDecimos->save();

                $pagoDecimos = PagoDecimos::All()->last();

                //CREA LA FACTURA CON EL ITEM
                $invoice= new Invoice;
                $invoice->invoice_id = 'FA'.$invoiceId;
                $invoice->invoice_type_id = 'PAYROL_INVOICE';
                $invoice->party_id_from = $contratacion->id_empleado;
                $invoice->party_id = $empresa->party_id;
                $invoice->product_store_id= $store->product_store_id;
                $invoice->status_id = 'INVOICE_READY';
                $invoice->currency_uom_id='USD';
                $invoice->due_date =  now()->toDateTimeString();
                $invoice->invoice_date = now()->toDateTimeString();
                $invoice->description = 'Factura decimo '. ($request->tipo == 'DECIMO_4TO' ? '4to': '3er').' sueldo '.$person->first_name.' '.$person->last_name;
                $invoice->last_updated_stamp = now()->toDayDateTimeString();
                $invoice->last_updated_tx_stamp = now()->toDayDateTimeString();
                $invoice->created_stamp = now()->toDayDateTimeString();
                $invoice->created_tx_stamp = now()->toDayDateTimeString();

                if($invoice->save()){

                    $inoviceItem = new InvoiceItem;
                    $inoviceItem->invoice_id = $invoice->invoice_id;
                    $inoviceItem->invoice_item_seq_id = '00001';
                    $inoviceItem->invoice_item_type_id = $request->tipo == 'DECIMO_4TO' ? 'DEC_4TO_PR' : 'DEC_3ERO_PR';
                    $inoviceItem->quantity = 1.000000;
                    $inoviceItem->amount = $total;
                    $inoviceItem->description = 'Decimo '. ($request->tipo == 'DECIMO_4TO' ? '4to': '3er').' sueldo '.$person->first_name.' '.$person->last_name;
                    $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                    $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_stamp = now()->toDayDateTimeString();
                    $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();

                    if($inoviceItem->save()){

                        $dataAcctg =[
                            'acctg_trans_type_id' => 'PAYROL_INVOICE',
                            'gl_fiscal_type_id' => 'ACTUAL',
                            'is_posted' => 'Y',
                            'party_id' => $invoice->party_id_from,
                            'invoice_id' => $invoice->invoice_id,
                            'created_by_user_login' => session('dataUsuario')['id_usuario_log'],
                            'last_modified_by_user_login' => session('dataUsuario')['id_usuario_log'],
                            'role_type_id' => 'EMPLOYEE',
                            'description' => 'Décimo '.($request->tipo == 'DECIMO_4TO' ?  'cuarto' : ' tercer').' sueldo '. $person->first_name .' '.$person->last_name
                        ];

                        // ASIENTO DECIMO //
                        $glAccountDebito = glAccountMapInvoice($inoviceItem->invoice_item_type_id);

                        if(
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->gl_account_id) || $glAccountDebito->gl_account_id=='')) &&
                            (!isset($glAccountDebito) || (!isset($glAccountDebito->default_gl_account_id) || $glAccountDebito->default_gl_account_id==''))
                        ){
                            throw new \Exception('<b>No existe una cuenta contable registrada para el item '.$inoviceItem->invoice_item_type_id.' de la factura </b> <br />');
                        }

                        $dataAcctg['debitos'][]= [
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => isset($glAccountDebito->gl_account_id) ? $glAccountDebito->gl_account_id : $glAccountDebito->default_gl_account_id,
                            'amount' =>  $total
                        ];

                        $glAccountCredito = glAccountMapPayment('PAYROL_PAYMENT'); // SUELDO POR PAGAR

                        if(
                            (!isset($glAccountCredito) || (!isset($glAccountCredito->gl_account_id) || $glAccountCredito->gl_account_id=='')) &&
                            (!isset($glAccountCredito) || (!isset($glAccountCredito->default_gl_account_id) || $glAccountCredito->default_gl_account_id==''))
                        ){
                            throw new \Exception('<b>No existe una cuenta contable mapeada para PAYROL_PAYMENT </b> <br />');
                        }

                        $dataAcctg['creditos'][]= [
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => $glAccountCredito->gl_account_id,
                            'amount' =>  $total
                        ];

                        $res = crearAcctgTrans($dataAcctg);

                        if(!$res['success']){
                            throw new \Exception('No se pudo crear el asiento contable del item '.$inoviceItem->invoice_item_type_id.' de la factura '. $res['msg']);
                        }
                        unset($dataAcctg);
                        // FIN ASIENTO DECIMO  //

                    }

                    // CREA EL  PAGO
                    $res = crearPago([
                        'payment_type_id' => 'PAYROL_PAYMENT',
                        'empresa' => $empresa,
                        'referencia' => $request->referencia,
                        'comentario' => 'Pago decimo '. ($request->tipo == 'DECIMO_4TO' ? '3er': '4to').' sueldo '.$person->first_name.' '.$person->last_name,
                        'tipo' => $request->tipo == 'DECIMO_4TO' ? 'decimo_cuarto' : 'decimo_tercero',
                        'id_registro' => $pagoDecimos->id_pago_decimos,
                        'monto' => $total,
                        'person' => $person,
                        'aplicado' =>true,
                        'fecha' => $request->tipo == 'DECIMO_4TO' ? Carbon::parse($invoice->due_date)->format('Y-07-d') : Carbon::parse($invoice->due_date)->format('Y-12-d')
                    ]);

                    if(!$res['success']){
                        $msgPersonal=true;
                        throw new \Exception('No se pudo generara el pago '. $res['msg']);
                    }

                    // CREA EL ASIENTO DEL PAGO
                    $paymentId = $res['paymentId'];

                    $dataAcctg =[
                        'acctg_trans_type_id' => 'OUTGOING_PAYMENT',
                        'gl_fiscal_type_id' => 'ACTUAL',
                        'is_posted' => 'Y',
                        'party_id' => $contratacion->id_empleado,
                        'payment_id' => $paymentId,
                        'description' =>  'Pago decimo '. ($request->tipo == 'DECIMO_4TO' ? '3er': '4to').' sueldo '.$person->first_name.' '.$person->last_name,
                        'created_by_user_login' => session('dataUsuario')['id_usuario_log'],
                        'last_modified_by_user_login' => session('dataUsuario')['id_usuario_log'],
                        'role_type_id' => 'BILL_FROM_VENDOR'
                    ];

                    $glAccountDebito = glAccountMapPayment('PAYROL_PAYMENT');
                    $dataAcctg['debitos'][]= [
                        'organization_party_id' => $empresa->party_id,
                        'gl_account_id' => $glAccountDebito->gl_account_id,
                        'amount' => $total
                    ];

                    // BANCOS
                    $dataAcctg['creditos'][] =[
                        'organization_party_id' => $empresa->party_id,
                        'gl_account_id' => $empresa->post_to_gl_account_id,
                        'amount' => $total
                    ];

                    $res = crearAcctgTrans($dataAcctg);
                    if(!$res['success']){
                        $msgPersonal=true;
                        throw new \Exception('No se pudo crear el asiento contable '. $res['msg']);
                    }

                    //APLICA EL PAGO A LA FACTURA
                    $resPayment = paymentApplication([
                        'paymentId' => $paymentId,
                        'facturaId' => $invoice->invoice_id,
                        'amount_applied' => $total
                    ]);

                    if(!$resPayment['success'])
                        throw new Exception('No se pudo aplicar el pago de la factura '.$invoice->invoice_id.' '.$resPayment['msg']);

                }
            }

            DB::commit();
            DB::connection($conexion)->commit();
            $msg = '<div class="alert alert-success" role="alert" style="margin: 10px">
                        Se han generado los pagos de los decimos correspondiente con éxito
                    </div>';
            $status = true;

        }catch(\Exception $e){

            DB::rollBack();
            DB::connection($conexion)->rollBack();

            $msg = '<div class="alert alert-danger" role="alert" style="margin: 10px">
                        Ha ocurrido un error al guardar generar los pagos <br /> '
                        .$e->getMessage().' '.(!isset($msgPersonal) ? $e->getFile().' '. $e->getLine() : '').'
                    </div>';
            $status = false;

        }

        return response()->json(['status'=>$status,'msg'=>$msg]);

    }

    public function formCashManagementAlcanceNomina(Request $request)
    {
        return view('layouts/views/nomina/partials/form_cash_management_alcance_nomina',[
            'Nomina' => Nomina::class,
            'alcancesNominas' => AlcanceNomina::whereNotIn('id_alcance_nomina',function($query){
                $query->select('id_registro')->from('referencia_pago')->where('tipo','alcance_nomina');
            })->get()
        ]);
    }

    public function fileCashManagmentAlcanceNomina(Request $request)
    {
        $dataFile='';

        $cuentaEmpresa = cuentaEmpresa();

        $alcancesNominas = AlcanceNomina::whereNotIn('id_alcance_nomina',function($query){
            $query->select('id_registro')->from('referencia_pago')->where('tipo','alcance_nomina');
        })->join('nomina as n','alcance_nomina.id_nomina','n.id_nomina')
        ->select('alcance_nomina.*','n.id_empleado')->get();

        foreach($alcancesNominas as $alcancesNomina){

            $dataContratacion = contratacionesCashManagement($alcancesNomina->id_empleado)->first();

            $dataFile.= "PA\t".str_pad($cuentaEmpresa->account_number,20)."\t1\t1\t".str_pad($dataContratacion->id_value,13)."\tUSD\t".str_pad(str_replace(['.',','],'',number_format($alcancesNomina->total,2)),13,'0',STR_PAD_LEFT)."\tCTA\t". str_pad($dataContratacion->enum_code2,13)."\t".$dataContratacion->tipo_cuenta."\t".str_pad($dataContratacion->account_number,20)."\t".$dataContratacion->codigo_identificacion."\t".str_pad($dataContratacion->id_value,14)."\t".str_pad("PAGO ALCANCE NÓMINA ".strtoupper($dataContratacion->empleado),40)."\t\n";

        }

        return base64_encode($dataFile);
    }

    public function storeReferenciaBancariaAlcanceNomina(Request $request)
    {
        $conexion = getConnection(0);

        DB::connection($conexion)->beginTransaction();
        DB::beginTransaction();

        try{

            $empresa = cuentaEmpresa();

            $alcancesNominas = AlcanceNomina::whereNotIn('id_alcance_nomina',function($query){
                $query->select('id_registro')->from('referencia_pago')->where('tipo','alcance_nomina');
            })->join('nomina as n','alcance_nomina.id_nomina','n.id_nomina')
                ->select('alcance_nomina.*','n.id_empleado')->get();

            foreach($alcancesNominas as $alcancesNomina){

                $person = getPerson($alcancesNomina->id_empleado);

                if(getRelacionDependencia($alcancesNomina->id_empleado)->relacion_dependencia){
                    $paymentTypeId = 'PAYROL_PAYMENT';
                }else{
                    $paymentTypeId = 'VENDOR_PAYMENT';
                }

                // CREA EL  PAGO
                $res = crearPago([
                    'payment_type_id' => $paymentTypeId,
                    'empresa' => $empresa,
                    'referencia' => $request->referencia,
                    'comentario' => 'Pago alcance de nómina '.$person->first_name.' '.$person->last_name,
                    'tipo' => 'alcance_nomina',
                    'id_registro' => $alcancesNomina->id_alcance_nomina,
                    'monto' => $alcancesNomina->total,
                    'person' => $person,
                    'aplicado' =>true,
                    'fecha' => now()->toDateString()
                ]);

                if(!$res['success']){
                    $msgPersonal=true;
                    throw new \Exception('No se pudo generara el pago '. $res['msg']);
                }

                // CREA EL ASIENTO DEL PAGO
                $paymentId = $res['paymentId'];

                $dataAcctg =[
                    'acctg_trans_type_id' => 'OUTGOING_PAYMENT',
                    'gl_fiscal_type_id' => 'ACTUAL',
                    'is_posted' => 'Y',
                    'party_id' => $alcancesNomina->id_empleado,
                    'payment_id' => $paymentId,
                    'description' =>  'Pago decimo '. ($request->tipo == 'DECIMO_4TO' ? '3er': '4to').' sueldo '.$person->first_name.' '.$person->last_name,
                    'created_by_user_login' => session('dataUsuario')['id_usuario_log'],
                    'last_modified_by_user_login' => session('dataUsuario')['id_usuario_log'],
                    'role_type_id' => 'BILL_FROM_VENDOR'
                ];

                $glAccountDebito = glAccountMapPayment($paymentTypeId);
                $dataAcctg['debitos'][]= [
                    'organization_party_id' => $empresa->party_id,
                    'gl_account_id' => $glAccountDebito->gl_account_id,
                    'amount' => $alcancesNomina->total
                ];

                // BANCOS
                $dataAcctg['creditos'][] =[
                    'organization_party_id' => $empresa->party_id,
                    'gl_account_id' => $empresa->post_to_gl_account_id,
                    'amount' => $alcancesNomina->total
                ];

                $res = crearAcctgTrans($dataAcctg);
                if(!$res['success']){
                    $msgPersonal=true;
                    throw new \Exception('No se pudo crear el asiento contable '. $res['msg']);
                }

                //APLICA EL PAGO A LA FACTURA
                $resPayment = paymentApplication([
                    'paymentId' => $paymentId,
                    'facturaId' => $alcancesNomina->invoice_id,
                    'amount_applied' => $alcancesNomina->total
                ]);

                if(!$resPayment['success'])
                    throw new Exception('No se pudo aplicar el pago de la factura '.$alcancesNomina->invoice_id.' '.$resPayment['msg']);
            }

            DB::commit();
            DB::connection($conexion)->commit();
            $msg = '<div class="alert alert-success" role="alert" style="margin: 10px">
                        Se han generado los pagos de los alcances correspondiente con éxito
                    </div>';
            $status = true;

        }catch(\Exception $e){

            DB::rollBack();
            DB::connection($conexion)->rollBack();

            $msg = '<div class="alert alert-danger" role="alert" style="margin: 10px">
                        Ha ocurrido un error al guardar generar los pagos <br /> '
                        .$e->getMessage().' '.(!isset($msgPersonal) ? $e->getFile().' '. $e->getLine() : '').'
                    </div>';
            $status = false;

        }

        return response()->json(['status'=>$status,'msg'=>$msg]);

    }

    public function formCashManagementLiquidacion(Request $request)
    {
        return view('layouts/views/nomina/partials/form_cash_management_liquidacion');
    }

    public function fileCashManagmentLiquidacion(Request $request)
    {
        $dataFile='';
        $cuentaEmpresa = cuentaEmpresa();

        $liquidaciones = Nomina::where('liquidacion',true)
                        ->where('fecha_nomina','>','2021-01-30')
                        ->whereIn('id_empleado',$request->empleados)
                        ->whereNotIn('id_nomina',function($query){
                            $query->select('id_registro')->from('referencia_pago')->where('tipo','liquidacion');
                        })->get();

        foreach($liquidaciones as $liquidacion){

            $dataContratacion = contratacionesCashManagement($liquidacion->id_empleado,3)->first();

            $dataFile.= "PA\t".str_pad($cuentaEmpresa->account_number,20)."\t1\t1\t".str_pad($dataContratacion->id_value,13)."\tUSD\t".str_pad(str_replace(['.',','],'',number_format($liquidacion->total,2)),13,'0',STR_PAD_LEFT)."\tCTA\t". str_pad($dataContratacion->enum_code2,13)."\t".$dataContratacion->tipo_cuenta."\t".str_pad($dataContratacion->account_number,20)."\t".$dataContratacion->codigo_identificacion."\t".str_pad($dataContratacion->id_value,14)."\t".str_pad("PAGO LIQUIDACIÓN ".strtoupper($dataContratacion->empleado),40)."\t\n";

        }

        return base64_encode($dataFile);
    }

    public function storeReferenciaBancariaLiquidacion(Request $request)
    {
        $conexion = getConnection(0);

        DB::connection($conexion)->beginTransaction();
        DB::beginTransaction();

        try{

            $empresa = cuentaEmpresa();

            $liquidaciones = Nomina::where('liquidacion',true)
                            ->where('fecha_nomina','>','2021-01-30')
                            ->whereIn('id_empleado',$request->empleados)
                            ->whereNotIn('id_nomina',function($query){
                                $query->select('id_registro')->from('referencia_pago')->where('tipo','liquidacion');
                            })->get();

            foreach($liquidaciones as $liquidacion){

                $person = getPerson($liquidacion->id_empleado);

                $relacionDependencia = getRelacionDependencia($liquidacion->id_empleado)->relacion_dependencia;

                if(getRelacionDependencia($liquidacion->id_empleado)->relacion_dependencia){
                    $paymentTypeId = 'PAYROL_PAYMENT';
                }else{
                    $paymentTypeId = 'VENDOR_PAYMENT';
                }

                // CREA EL  PAGO
                $res = crearPago([
                    'payment_type_id' => $paymentTypeId,
                    'empresa' => $empresa,
                    'referencia' => $request->referencia,
                    'comentario' => 'Pago '.($relacionDependencia ? 'liquidación' : 'ingreso de nómina').' '.$person->first_name.' '.$person->last_name,
                    'tipo' => 'liquidacion',
                    'id_registro' => $liquidacion->id_nomina,
                    'monto' => $liquidacion->total,
                    'person' => $person,
                    'aplicado' =>true,
                    'fecha' => now()->toDateString()
                ]);

                if(!$res['success']){
                    $msgPersonal=true;
                    throw new \Exception('No se pudo generara el pago '. $res['msg']);
                }

                // CREA EL ASIENTO DEL PAGO
                $paymentId = $res['paymentId'];

                $dataAcctg =[
                    'acctg_trans_type_id' => 'OUTGOING_PAYMENT',
                    'gl_fiscal_type_id' => 'ACTUAL',
                    'is_posted' => 'Y',
                    'party_id' => $liquidacion->id_empleado,
                    'payment_id' => $paymentId,
                    'description' =>  'Pago decimo '. ($request->tipo == 'DECIMO_4TO' ? '3er': '4to').' sueldo '.$person->first_name.' '.$person->last_name,
                    'created_by_user_login' => session('dataUsuario')['id_usuario_log'],
                    'last_modified_by_user_login' => session('dataUsuario')['id_usuario_log'],
                    'role_type_id' => 'BILL_FROM_VENDOR'
                ];


                $glAccountDebito = glAccountMapPayment($paymentTypeId);
                $dataAcctg['debitos'][]= [
                    'organization_party_id' => $empresa->party_id,
                    'gl_account_id' => $glAccountDebito->gl_account_id,
                    'amount' => $liquidacion->total
                ];

                // BANCOS
                $dataAcctg['creditos'][] =[
                    'organization_party_id' => $empresa->party_id,
                    'gl_account_id' => $empresa->post_to_gl_account_id,
                    'amount' => $liquidacion->total
                ];

                $res = crearAcctgTrans($dataAcctg);
                if(!$res['success']){
                    $msgPersonal=true;
                    throw new \Exception('No se pudo crear el asiento contable '. $res['msg']);
                }

                //APLICA EL PAGO A LA FACTURA

                $facturasId = explode(',',$liquidacion->id_factura);

                $montoPago = $liquidacion->total;
                $montoP = $liquidacion->total;

                foreach($facturasId  as $facturaId){

                    $invoice = Invoice::where('invoice_id',$facturaId)
                    ->select(
                        DB::raw("
                            (select sum(invit.quantity*invit.amount) from invoice_item as invit where invit.invoice_id = invoice.invoice_id) -
                            (  case when
                                    (select sum(pa.amount_applied) from payment_application as pa where pa.invoice_id =  invoice.invoice_id) is null
                                then 0
                                else
                                    (select sum(pa.amount_applied) from payment_application as pa where pa.invoice_id =  invoice.invoice_id) end
                            ) as saldo
                        ")
                    )->first();

                    if($invoice->saldo <= $montoP){

                        $montoPago = $invoice->saldo;

                    }

                    if($montoP > 0){

                        $resPayment = paymentApplication([
                            'paymentId' => $paymentId,
                            'facturaId' => $facturaId,
                            'amount_applied' => $montoPago
                        ]);

                        if(!$resPayment['success'])
                            throw new Exception('No se pudo aplicar el pago de la factura '.$liquidacion->id_factura.' '.$resPayment['msg']);

                    }

                    $montoP -= $invoice->saldo;
                }

            }

            DB::commit();
            DB::connection($conexion)->commit();
            $msg = '<div class="alert alert-success" role="alert" style="margin: 10px">
                        Se han generado los pagos de los alcances correspondiente con éxito
                    </div>';
            $status = true;

        }catch(\Exception $e){

            DB::rollBack();
            DB::connection($conexion)->rollBack();

            $msg = '<div class="alert alert-danger" role="alert" style="margin: 10px">
                        Ha ocurrido un error al guardar generar los pagos <br /> '
                        .$e->getMessage().' '.(!isset($msgPersonal) ? $e->getFile().' '. $e->getLine() : '').'
                    </div>';
            $status = false;

        }

        return response()->json(['status'=>$status,'msg'=>$msg]);

    }

}
