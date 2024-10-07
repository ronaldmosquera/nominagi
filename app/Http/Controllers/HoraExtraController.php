<?php

namespace App\Http\Controllers;

use App\Models\AnnoMesFeriado;
use App\Models\FechaFeriado;
use App\Models\Person;
use App\Models\ForeginContrataciones;
use Illuminate\Http\Request;
use App\Models\Contrataciones;
use App\Models\HorasExtra;
use App\Models\ConfiguracionVariablesEmpresa;
use App\Models\AsignacionHorario;
use Carbon\Carbon;
use Validator;
use DB;

class HoraExtraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

      /*  $succesHorasExtras = Contrataciones::where('id_empleado',session('dataUsuario')['id_empleado'])
            //->join('contrato as c','contrataciones.id_tipo_contrato','=','c.id_contrato')
            ->join('tipo_contrato as tc','contrataciones.id_tipo_contrato','=','tc.id_tipo_contrato')
            ->where('contrataciones.estado',1)->select('tc.horas_extras')->first();*/

        $horaExtraCargoConfianza = Contrataciones::where([
            ['id_empleado',session('dataUsuario')['id_empleado']],
            ['estado',1]
        ])->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')
            ->join('cargos as c','dc.id_cargo','c.id_cargo')->select('cargo_confianza')->first();

         /*   if (!$succesHorasExtras->horas_extras)
                flash('<div>
                                    <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                                    Su tipo de contrato no le permite hacer peticiones de horas extras
                               </div>')->error();*/
	
            if($horaExtraCargoConfianza->cargo_confianza) {
                flash('<div>
                                <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                                Usted posee un cargo de confianza por lo cual no puede hacer peticiones de horas extras
                           </div>')->error();
            }


        $data= HorasExtra::where('id_empleado',session('dataUsuario')['id_empleado']);

        if(!empty($request->desde) && !empty($request->hasta))
            $data->whereBetween('fecha_solicitud',[$request->desde,$request->hasta]);


        return view('layouts.views.horas_extras.list',[
                'dataHorasExtras' =>$data->where('estado',$request->estado != null ? $request->estado : 1)
                    ->orderBy('id_horas_extras','Desc')->paginate(10),
                'success' => $horaExtraCargoConfianza->cargo_confianza ? !$horaExtraCargoConfianza->cargo_confianza : true/*$succesHorasExtras->horas_extras*/
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $existHoraExtra = HorasExtra::where([
            ['id_empleado',session('dataUsuario')['id_empleado']],
            ['fecha_solicitud',Carbon::now()->format('Y-m-d')]
        ])->count();

        /*if(empty($request->id_horas_extras))
            if($existHoraExtra > 0)
                return '<div class="alert alert-danger" role="alert" style="margin: 0">
                            <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                            Usted posee una solicitud de horas extras para este día, debe eleminarla para poder realizar otra
                        </div>';*/

        return view('layouts.views.horas_extras.partials.form_horas_extras',
            [
                'dataVariables' => ConfiguracionVariablesEmpresa::select('hora_extra_entre_semana','hora_extra_fin_semana')->first(),
                'id_horas_extras' => $request->id_horas_extras
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // dd($request->all());
        $valida =  Validator::make($request->all(), [
            'arrData' => 'required|Array',
        ]);

        $msg='';
        $status = '';
        if(!$valida->fails()) {
            foreach ($request->arrData as $data){
                $arrFecha = explode("/",$data[0]);

                $objHorasExtra = empty($data[5]) ? new HorasExtra : HorasExtra::find($data[5]);
                $objHorasExtra->id_empleado      = $data[6];
                $objHorasExtra->fecha_solicitud  = $arrFecha[2]."/".$arrFecha[1]."/".$arrFecha[0];
                $objHorasExtra->desde            = $data[1];
                $objHorasExtra->hasta            = $data[2];
                $objHorasExtra->cantidad_horas   = $data[3];
                $objHorasExtra->comentarios      = $data[4];
                $objHorasExtra->invoice_item_type_id = 'GASTO_HON_PROF';

                if($objHorasExtra->save()){

                    $msg .= '<div class="alert alert-success" role="alert" style="margin-bottom: 10px">
                                Se ha enviado la solicitud de horas extras de fecha '.$objHorasExtra->fecha_solicitud.' con exito
                            </div>';
                    $status = 1;

                }else{
                    $msg .= '<div class="alert alert-danger" role="alert" style="margin-bottom: 10px">
                                No pudo ser enviada la solicitud de horas extras de fecha '.$objHorasExtra->fecha_solicitud.' intente nuevamente
                            </div>';
                    $status = 0;
                }
            }
        }else {

            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
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
        return [
            'asignacionHorario' => AsignacionHorario::where([
                ['id_empleado',$request->id_empleado],
                ['fecha',$request->fecha]
            ])->select('hasta','desde')->first(),
            'fin_semana'=> Carbon::parse($request->fecha)->isSaturday() || Carbon::parse($request->fecha)->isSunday()
        ];
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
    public function update(Request $request)
    {
        $msg = '';
        $a = $request->arrIdHorasExtras;

        $valida =  Validator::make($request->all(), [
            'arrIdHorasExtras' => 'required|Array|min:1',
        ],[
            'arrIdHorasExtras.required' => 'Debe seleccionar al menos una solicitud de horas extras',
            'arrIdHorasExtras.min' => 'Debe seleccionar al menos una solicitud de horas extras',
            'arrIdHorasExtras.Array' => 'Debe seleccionar al menos una solicitud de horas extras',
        ]);

        $msg='';
        $status = '';

        if(!$valida->fails()) {

            foreach ($a as $key => $idHorasExtras){

                $dataHorasExtras = HorasExtra::find($idHorasExtras);
                $estadoHoraExtra = HorasExtra::where('id_horas_extras',$idHorasExtras)->select('estado','fecha_solicitud')->first();
                //dd($estadoHoraExtra[0]->estado);
                $dataHorasExtras->estado = $estadoHoraExtra->estado == 0 ? 1 : 0;

                if($dataHorasExtras->update()){
                    $msg = '<div class="alert alert-success" role="alert" style="margin-bottom: 10px">
                                  Se han aprobado las horas extras con exito
                              </div>';
                    $status = 1;

                }else{
                    $msg = '<div class="alert alert-danger" role="alert" style="margin-bottom: 10px">
                                    No pudieron ser aprobadas las horas extras, intente nuevamente
                                </div>';
                    $status = 0;
                }
            }

        }else {

            $status = 0;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
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


        return response()->json(['status'=>$status,'msg'=>$msg]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        $DeletehoraExtra = HorasExtra::destroy($request->id_horas_extras);
        if($DeletehoraExtra){
            $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                     <i class="fa fa-check-circle" aria-hidden="true"></i>
                     El contrato ha sido eliminado con éxito
                   </div>';
        }else{
            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                    <i class="fa fa-window-close" aria-hidden="true"></i>
                     Hubo un error al eliminar el contrato, intente nuevamente
                   </div>';
        }
        return $msg;
    }

    public function delete_hora_extra(Request $request){

        $DeletehoraExtra = HorasExtra::destroy($request->id_horas_extras);
        if($DeletehoraExtra){
            $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                     <i class="fa fa-check-circle" aria-hidden="true"></i>
                     La hora extra ha sido eliminada con éxito
                   </div>';
        }else{
            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                    <i class="fa fa-window-close" aria-hidden="true"></i>
                     Hubo un error al eliminar la hora extra, intente nuevamente
                   </div>';
        }
        return $msg;

    }

    public function add_inputs(Request $request){
        $horas= "";
        if(!empty($request->id_hora_extra)) {
            $horaExtra = HorasExtra::where('id_horas_extras', $request->id_hora_extra)->first();
            //dd($horaExtra->id_empleado,$horaExtra->fecha_solicitud);
            $horas = AsignacionHorario::where([
                ['id_empleado', $horaExtra->id_empleado],
                ['fecha', $horaExtra->fecha_solicitud]
            ])->select('desde', 'hasta')->first();
        }
        return view('layouts.views.horas_extras.partials.inputs_horas_extras',
            [
                'dataContrataciones' => Contrataciones::where([
                    ['id_empleado',$request->id_empleado],
                    ['estado',1]
                ])->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','=','dc.id_contrataciones')
                    ->select('dc.hora_salida')
                    ->first(),
                'id' => $request->cant_inputs,
                'dataHoraExtra' =>  HorasExtra::where('id_horas_extras', $request->id_hora_extra)->first(),
                'horas' => $horas
            ]);
    }

    public function adminHorasExtras(Request $request){
        $empleadosActivos = ForeginContrataciones::join('person as p','contrataciones.party_id','p.party_id')
            ->where('contrataciones.estado',1)
            ->whereNotIn('contrataciones.party_id',[session('dataUsuario')['id_empleado']])
            ->select('p.party_id')->distinct()->pluck('p.party_id');

        if(!isset($request->id_empleado)){
            $data= HorasExtra::whereIn('id_empleado',$empleadosActivos->toArray());
        }else{
            $data= HorasExtra::where('id_empleado',$request->id_empleado);
        }

        if(isset($request->desde) && isset($request->hasta))
            $data->whereBetween('fecha_solicitud',[$request->desde,$request->hasta]);


        $a = $data->orderBy('id_horas_extras','Desc')
             ->where('estado',!isset($request->estado) ? 0 : $request->estado)->get();

        $arrData = [];

        for($i=0;$i<count($a);$i++){

            $b = Person::where('party_id',$a[$i]->id_empleado)->first();
            $arrData[] = [
                'id_horas_extras'       => $a[$i]->id_horas_extras,
                'id_empleado'           => $a[$i]->id_empleado,
                'fecha_solicitud'       => $a[$i]->fecha_solicitud,
                'desde'                 => $a[$i]->desde,
                'hasta'                 => $a[$i]->hasta,
                'cantidad_horas'        => $a[$i]->cantidad_horas,
                'comentarios'           => $a[$i]->comentarios,
                'comentarios_respuesta' => $a[$i]->comentarios_respuesta,
                'estado'                => $a[$i]->estado,
                'nombre'                => $b->first_name." ".$b->last_name
            ];
        }

        return view('layouts.views.horas_extras.admin.list',
        [
            'dataHorasExtras' => manualPagination($arrData,10),
            'dataEmpleados'   => $empleadosActivos,
            'tiempoAprovHe' => ConfiguracionVariablesEmpresa::first()->tiempo_aprov_he,
        ]);

    }

    public function responderComentario(Request $request){
        return view('layouts.views.horas_extras.admin.form_responder_comentario',
            ['idHoraExtra'=>$request->id_hora_extra]);
    }

    public function storeRespuestaComentario(Request $request){

        $objHorasExtra = HorasExtra::find($request->idHoraExtra);
        $objHorasExtra->comentarios_respuesta = $request->comentario;
        $objHorasExtra->estado                = 2;

        if($objHorasExtra->save()){

            $msg = '<div class="alert alert-success" role="alert" style="margin-bottom: 10px">
                                Se ha enviado el comentario con exito
                            </div>';
            $status = 1;

        }else{
            $msg = '<div class="alert alert-danger" role="alert" style="margin-bottom: 10px">
                                No pudo ser enviado el comentario intente nuevamente
                            </div>';
            $status = 0;
        }
        return response()->json(['status'=>$status,'msg'=>$msg]);
    }

    public function configurar_feriados(Request $request){
        return view('layouts.views.horarios_empleados.partials.feriados');
    }

    public function input_feriados(Request $request){

        return view('layouts.views.horarios_empleados.partials.inputs_feriados',[
            'cant_input' => $request->cant_inputs
        ]);

    }

    public function store_fecha_feriado(Request $request){

        $valida =  Validator::make($request->all(), [
            'arr_datos' => 'required|Array',
        ]);

        $msg='';
        $status = '';
        if(!$valida->fails()) {

            $dataFeriados = AnnoMesFeriado::where('fecha',$request->anno_mes_feriado)->first();
            if(isset($dataFeriados->id_anno_mes_feriado) && $dataFeriados->id_anno_mes_feriado !="")
                AnnoMesFeriado::destroy($dataFeriados->id_anno_mes_feriado);

            $objAnnoMesFeriado = new AnnoMesFeriado;
            $objAnnoMesFeriado->fecha = $request->anno_mes_feriado;
            if($objAnnoMesFeriado->save()){
                $modelAnnoMesFeriado = AnnoMesFeriado::all()->last();
                foreach ($request->arr_datos as $data) {
                    $objFechaFeriado = new FechaFeriado;
                    $objFechaFeriado->id_anno_mes_feriado = $modelAnnoMesFeriado->id_anno_mes_feriado;
                    $objFechaFeriado->fecha = $data['fecha_feriado'];
                    if($objFechaFeriado->save()){
                        $msg = "<div class='alert alert-success' role='alert' style='margin: 0'>
                                    Se han guardado los feriados con éxito
                                </div>";
                        $status = 1;
                    }else{
                        AnnoMesFeriado::destroy($modelAnnoMesFeriado->id_anno_mes_feriado);
                        $msg = "<div class='alert alert-success' role='alert' style='margin: 0'>
                                    Ha ocurrido un error al guardar los feriados, intente nuevamente
                                </div>";
                        $status = 0;
                    }
                }
            }else{
                $msg = "<div class='alert alert-success' role='alert' style='margin: 0'>
                                    Ha ocurrido un error al guardar los feriados, intente nuevamente
                                </div>";
                $status = 0;
            }
        }
        return response()->json(['status'=>$status,'msg'=>$msg]);
    }

    public function search_anno_mes_feriado(Request $request){

        return view('layouts.views.horarios_empleados.partials.inputs_feriados',[
            'fechas_feriado' => AnnoMesFeriado::where('anno_mes_feriado.fecha',$request->fecha_anno_mes_feriado)
                ->join('fecha_feriado as ff','anno_mes_feriado.id_anno_mes_feriado','ff.id_anno_mes_feriado')
                ->select('anno_mes_feriado.fecha as f_anno_mes_feriado','ff.fecha as fecha_feriado')->get()

        ]);
    }

    public function search_feriado(Request $request){
        return  [
            'annos_mes_feriado' => AnnoMesFeriado::where('anno_mes_feriado.fecha',$request->fecha_anno_mes_feriado)
                ->join('fecha_feriado as ff','anno_mes_feriado.id_anno_mes_feriado','ff.id_anno_mes_feriado')
                ->select('anno_mes_feriado.fecha as f_anno_mes_feriado','ff.fecha as fecha_feriado')->get(),
            'asignacion_horario' => AsignacionHorario::where([
                ['id_empleado',$request->id_empleado],
                ['fecha', $request->fecha_solicitud]
            ])->first(),
            'fin_semana'=> Carbon::parse($request->fecha_solicitud)->isSaturday() || Carbon::parse($request->fecha_solicitud)->isSunday(),
            'dias_habiles' => ConfiguracionVariablesEmpresa::select('tiempo_carga_he')->first()->tiempo_carga_he
        ];

    }


}
