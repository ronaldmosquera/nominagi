<?php

namespace App\Console\Commands;

use App\Models\DetalleContratacion;
use Illuminate\Console\Command;
use App\Models\Contrataciones;
use App\Models\ForeginContrataciones;
use App\Models\FinalizacionContratacion;
use App\Models\Nomina;
use App\Models\Person;
use App\Models\Comisiones;
use App\Models\Consumos;
use App\Models\Anticipos;
use App\Models\OtrosDescuentos;
use Carbon\Carbon;


class TerminarContrataciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Terminar:Contrato';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Este comando verifica la fecha de vencimiento de las contrataciones que no estan bajo reación de dependencia y si coinciden con la actual los cambia de estado a 3 (TERMINADOS)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       /* $dataContrataciones = Contrataciones::where('estado',1)
            ->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','=','dc.id_contrataciones')
            ->select('dc.fecha_expedicion_contrato','dc.id_contrataciones','dc.duracion')->get();

        foreach($dataContrataciones as $contrataciones){

            if($contrataciones->duracion !== null && Carbon::parse(now()->format('Y-m-d'))->diffInDays($contrataciones->fecha_expedicion_contrato) == $contrataciones->duracion){

                $dataContratacion = Contrataciones::where([
                    ['contrataciones.id_contrataciones',$contrataciones->id_contrataciones],
                    ['contrataciones.estado',1],
                    ['contrataciones.id_tipo_contrato_descripcion',2]
                ])->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')
                    ->join('tipo_contrato as tc','contrataciones.id_tipo_contrato','tc.id_tipo_contrato')
                    ->join('cargos as c','dc.id_cargo','c.id_cargo')
                    ->select('id_empleado','vacaciones','fecha_expedicion_contrato','contrataciones.id_contrataciones','salario','tc.horas_extras','c.nombre')->first();

                //$ultimoSalario   = Nomina::where('id_empleado',$dataContratacion->id_empleado)->select('total')->get()->last();

                $dataPerson = Person::where('person.party_id',$dataContratacion->id_empleado)
                    ->join('party_identification as pi','person.party_id','pi.party_id')
                    ->join('party_identification_type as pit','pi.party_identification_type_id','pit.party_identification_type_id')
                    ->select('first_name','last_name','id_value','pit.description','person.party_id')->first();

                $montoHorasExtras = "N/A";
                $montoDecimoTercerSueldo = "N/A";
                $montoDecimoCuartoSueldo = "N/A";
                $montoVacaciones = "N/A";
                $montoDesahucio = "N/A";
                $montoDespidoIntempestivo = "N/A";
                $montoBonosFijos = 0.00;

                ////////// SALARIO //////////

                $existNomina = Nomina::where('id_empleado',$dataContratacion->id_empleado)->get();
                if(count($existNomina)>0){
                    $diasTrabajadosMesActual = Carbon::parse(Carbon::now()->toDateString())->diffInDays(Carbon::now()->format('Y-m-01')) + 1;
                }else{
                    $diasTrabajadosMesActual =  Carbon::parse(Carbon::now()->toDateString())->diffInDays($dataContratacion->fecha_expedicion_contrato) + 1;
                }

                if(Carbon::now()->format('m') == "02" && $diasTrabajadosMesActual == 28 || Carbon::now()->format('m') == "02" && $diasTrabajadosMesActual == 29){
                    $dias = 30;
                }else{
                    $diasTrabajadosMesActual == 31 ? $dias = 30 : $dias = $diasTrabajadosMesActual;
                }

                ////////// FIN SALARIO //////////


                ////////// OTROS CÁCULOS //////////

                $dataContratacion->horas_extras == true
                    ? $montoHorasExtras = number_format(getHorasExtras($dataContratacion->id_empleado, 1,$dataContratacion->fecha_expedicion_contrato,Carbon::now()->toDateString()),2,".","")
                    : "";


                $comisiones = Comisiones::where([
                    ['id_empleado',$dataContratacion->id_empleado],
                    ['pagada',0]
                ])->join('tipo_comisiones as tc','comisiones.id_tipo_comision','tc.id_tipo_comision')->get();

                $montoComisiones = 0.00;
                foreach ($comisiones as $comision){
                    $montoComisiones += number_format($comision->estandar,2,".","");

                    $objComsiones = Comisiones::find($comision->id_comisiones);
                    $objComsiones->pagada = 1;
                    $objComsiones->save();
                }

                //--------------------------//
                $consumos = Consumos::where([
                    ['id_empleado',$dataContratacion->id_empleado],
                    ['estado',0],
                ])->get();

                $montoConsumos = 0.00;
                foreach ($consumos as $c) {
                    $montoConsumos += number_format($c->monto_descuento,2,".","");

                    $objConsumo = Consumos::find($c->id_consumo);
                    $objConsumo->estado = 1;
                    $objConsumo->save();
                }
                //--------------------------//

                //--------------------------//
                $anticipos = Anticipos::where([
                    ['id_empleado',$dataContratacion->id_empleado],
                    ['estado',1]
                ])->get();

                $montoAnticipos = 0.00;
                foreach ($anticipos as $a){
                    $montoAnticipos += number_format($a->cantidad,2,".","");

                    $objAnticipos = Anticipos::find($a->id_anticipo);
                    $objAnticipos->estado = 3;
                    $objAnticipos->save();
                }
                //--------------------------//

                //--------------------------//
                $montoDescuentos = 0.00;
                $otrosDescuentos = OtrosDescuentos::where([
                    ['id_empleado',$dataContratacion->id_empleado],
                    ['descontado',0]
                ])->get();

                foreach($otrosDescuentos as $descuento){
                    $montoDescuentos += $descuento->cantidad;

                    $objDecuentos = OtrosDescuentos::find($descuento->id_descuento);
                    $objDecuentos->descontado = 1;
                    $objDecuentos->save();
                }
                //--------------------------//

                ////////// FIN OTROS CÁCULOS //////////
                $montoSalario = number_format(($dataContratacion->salario/30) * $dias,2,".","");

                $arrBonosFijos   = getBonosFijos($dataContratacion->id_contrataciones,true,$diasTrabajadosMesActual,Carbon::now()->toDateString());
                $arrPrestamos    = getPrestamos($dataContratacion->id_contrataciones,1,true,Carbon::now()->toDateString());

                $dataLiquidacion = [
                    'nombreEmpleado'          => $dataPerson->first_name . " " . $dataPerson->last_name,
                    'documento'               => $dataPerson->description,
                    'identificacion'          => $dataPerson->id_value,
                    'cargo'                   => $dataContratacion->nombre,
                    'idContrato'              => $dataContratacion->id_contrataciones,
                    'montoDecimoTercerSueldo' => $montoDecimoTercerSueldo,
                    'montoDecimoCuartoSueldo' => $montoDecimoCuartoSueldo,
                    'montoVacaciones'         => $montoVacaciones,
                    'montoDesahucio'          => $montoDesahucio,
                    'montoDespidoIntempestivo'=> $montoDespidoIntempestivo,
                    'montoHorasExtras'        => $montoHorasExtras,
                    'montoComisiones'         => $montoComisiones,
                    'arrPrestamos'            => $arrPrestamos['arrPrestamos'],
                    'arr_bonos_fijos'         => $arrBonosFijos['arrBonosFijos'],
                    'montoConsumos'           => number_format($montoConsumos,2,".",""),
                    'montoAnticipos'          => $montoAnticipos,
                    'montoSalario'            => $montoSalario,
                    'montoDescuentos'         => $montoDescuentos,
                    'diasTrabajadosMesActual' => $diasTrabajadosMesActual,
                    'montoTotalIngresos'      => $montoSalario + $montoComisiones + (is_numeric($montoDecimoTercerSueldo) ? $montoDecimoTercerSueldo : 0) +(is_numeric($montoDecimoCuartoSueldo) ? $montoDecimoCuartoSueldo : 0) + (is_numeric($montoVacaciones) ? $montoVacaciones : 0) + (is_numeric($montoDesahucio) ? $montoDesahucio : 0) + (is_numeric($montoDespidoIntempestivo) ? $montoDespidoIntempestivo : 0) + (is_numeric($montoHorasExtras) ? $montoHorasExtras : 0) + ($arrBonosFijos['montoBonosFijos']),
                    'montoTotalEgresos'       => $montoConsumos + $montoAnticipos + $montoDescuentos + $arrPrestamos['montoPrestamos'],
                    'montoTotalARecibir'      => $montoSalario + $montoComisiones + (is_numeric($montoDecimoTercerSueldo) ? $montoDecimoTercerSueldo : 0) + (is_numeric($montoDecimoCuartoSueldo) ? $montoDecimoCuartoSueldo : 0) + (is_numeric($montoVacaciones) ? $montoVacaciones : 0) + (is_numeric($montoDesahucio) ? $montoDesahucio : 0) + (is_numeric($montoDespidoIntempestivo) ? $montoDespidoIntempestivo : 0) + (is_numeric($montoHorasExtras) ? $montoHorasExtras : 0) + ($arrBonosFijos['montoBonosFijos']) - $montoConsumos - $montoAnticipos - $montoDescuentos - $arrPrestamos['montoPrestamos']
                ];

                $contrataciones = Contrataciones::where('id_contrataciones',$contrataciones->id_contrataciones);

                if($contrataciones->update(['estado',3])){

                    $hoy = Carbon::now()->toDateString();
                    //$modelContrataciones = Contrataciones::all()->last();
                    $objFinalizacionContratacion= new FinalizacionContratacion;
                    $objFinalizacionContratacion->id_contrataciones = $contrataciones->id_contrataciones;
                    $objFinalizacionContratacion->fecha_finalizacion = Carbon::now()->toDateString();
                    $objFinalizacionContratacion->id_tipo_finalizacion = 1;

                    if($objFinalizacionContratacion->save()){

                        $modelFinalizacionContratacion = FinalizacionContratacion::all()->last();
                        $dataContratacionesForeign = ForeginContrataciones::find($contrataciones->id_contrataciones);
                        $dataContratacionesForeign->estado = 3;

                        if($dataContratacionesForeign->save()){

                            $modelForeignContrataciones = ForeginContrataciones::all()->last();
                            $view = \View::make('layouts.views.nomina.partials.rol_pago_liquidacion', compact('dataLiquidacion'))->render();
                            $pdf = \App::make('dompdf.wrapper');
                            $pdf->loadHTML($view);
                            $nombre_archivo = $hoy."_liquidacion_".$dataLiquidacion['identificacion']."_".$dataLiquidacion['nombreEmpleado'].".pdf";
                            $pdf->save(public_path('roles_pago') . '/'.$nombre_archivo);

                            $idEmpleado = Contrataciones::where('id_contrataciones',$dataLiquidacion['idContrato'])->select('id_empleado')->first()->id_empleado;

                            $objImagenRoles = new ImagenesRoles;
                            $objImagenRoles->fecha_nomina  = Carbon::parse($hoy)->format("Y-m-05");
                            $objImagenRoles->nombre_imagen = $nombre_archivo;
                            $objImagenRoles->id_empleado   = $idEmpleado;
                            $objImagenRoles->tipo          = 2;

                            if($objImagenRoles->save()){

                                $modelImagenRoles = ImagenesRoles::all()->last();

                                $objNomina = new Nomina;
                                $objNomina->id_empleado  = $idEmpleado;
                                $objNomina->fecha_nomina = Carbon::parse($hoy)->format('Y-m-05');
                                $objNomina->total        = number_format($dataLiquidacion['montoTotalARecibir'],2,".","");
                                $objNomina->id_contrataciones = $dataLiquidacion['idContrato'];

                                if($objNomina->save()){

                                    flash('<i class="fa fa-exclamation-circle" aria-hidden="true"></i> La liquidación se ha generado con exito y a continuación se muestra el rol de pago')->success();
                                    return view('layouts.views.nomina.partials.rol_pago_liquidacion',[
                                        'dataLiquidacion' => $dataLiquidacion
                                    ]);

                                }else{
                                    FinalizacionContratacion::destroy($modelFinalizacionContratacion->id_finalizacion_contrataciones);
                                    Contrataciones::where('id_contrataciones',$contrataciones->id_contrataciones)->update(['estado'=>1]);
                                    ForeginContrataciones::where('id_contrataciones',$modelForeignContrataciones->id_contrataciones)->update(['estado'=>1]);
                                    ImagenesRoles::destroy($modelImagenRoles->id_imagen_rol);
                                }

                            }else {
                                ForeginContrataciones::where('id_contrataciones', $modelForeignContrataciones->id_contrataciones)->update(['estado' => 1]);
                                FinalizacionContratacion::destroy($modelFinalizacionContratacion->id_finalizacion_contrataciones);
                                Contrataciones::where('id_contrataciones', $contrataciones->id_contrataciones)->update(['estado' => 1]);
                            }

                            info("ID Contratatacion ".$contrataciones->id_contrataciones." terminada con exito");

                        }else{
                            FinalizacionContratacion::destroy($modelFinalizacionContratacion->id_finalizacion_contrataciones);
                            Contrataciones::where('id_contrataciones', $contrataciones->id_contrataciones)->update(['estado' => 1]);
                        }
                    }
                }else{

                }
            }

        }*/

    }
}
