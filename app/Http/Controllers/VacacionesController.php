<?php

namespace App\Http\Controllers;

use App\Models\DetalleContratacion;
use App\Models\Vacaciones;
use Illuminate\Http\Request;
use App\Models\Contrataciones;
use App\Models\ConfiguracionVariablesEmpresa;
use  App\Mail\SolicitudVacaciones;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Validator;

class VacacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dataContratacion = Contrataciones::where('id_empleado',session('dataUsuario')['id_empleado'])
            ->join('contrato as c','contrataciones.id_tipo_contrato','=','c.id_contrato')
            ->join('tipo_contrato as tc','c.id_tipo_contrato','=','tc.id_tipo_contrato')
            ->join('detalles_contrataciones as dc', 'contrataciones.id_contrataciones','=','dc.id_contrataciones')
            ->where('contrataciones.estado',1)
            ->where('contrataciones.id_tipo_contrato_descripcion',2)
            //->select('tc.vacaciones','dc.fecha_expedicion_contrato')
            ->first();

        $data = Vacaciones::where('id_empleado',session('dataUsuario')['id_empleado']);

        $fechaExpedicionContrato = Carbon::parse($dataContratacion->fecha_expedicion_contrato);
        $annosDiferencia = Carbon::now()->diffInYears($fechaExpedicionContrato);
        $message =false;

        //if(!$dataContratacion->relacion_dependencia)
            //flash('<div><i class="fa fa-exclamation-circle"></i> Su tipo de contrato no le permite hacer peticiones de vacaciones </div>')->error();

        if(!$dataContratacion->relacion_dependencia){
            if($dataContratacion->duracion <= 364){
                flash('<div><i class="fa fa-exclamation-circle"></i> Su contratación es menor a 1 año, esta debe ser mayor a 1 año para realizar solicitud de vacaciones</div>')->error();
                $message = true;
            }
        }

        //if($annosDiferencia < 1 && !$dataContratacion->relacion_dependencia)
            //flash('<div><i class="fa fa-exclamation-circle"></i> Debe poseer almenos 1 año de antiguedad para poder realizar peticiones de vacaciones</div>')->error();

        if(!empty($request->desde) && !empty($request->hasta))
            $data->whereBetween('fecha_inicio',[$request->desde,$request->hasta]);


        return view('layouts.views.vacaciones.list',
            [
                'dataVacaciones'=>$data->where('estado',$request->estado != null ? $request->estado : 0)
                    ->orderBy('id_vacaciones','Desc')->paginate(10),
                'message' => $message,
                //'annosDiferencia' => $annosDiferencia
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('layouts.views.vacaciones.partials.form_vacaciones',
            [
                'dataVacaciones' => Vacaciones::where('id_vacaciones',$request->id_vacaciones)->first(),
               //'id_horas_extras' => $request->id_horas_extras
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
        $valida =  Validator::make($request->all(), [
            'fecha_inicio' => 'required',
            'fecha_fin'    => 'required',
            'cant_dias'    => 'required',
            'entre_semana' => 'required',
            'fin_semana'   => 'required',
            /* 'periodo_desde'      => 'required',
            'periodo_hasta'      => 'required' */
        ]);

        $msg='';
        $status = '';
        if(!$valida->fails()) {

            empty($request->id_vacacion) ? $objVacaciones = new Vacaciones : $objVacaciones = Vacaciones::find($request->id_vacacion);
            in_array('ADMIN',session('dataUsuario')['user_type']) ? $objVacaciones->id_empleado  =  session('dataUsuario')['id_empleado'] : '';
            $objVacaciones->fecha_inicio      = $request->fecha_inicio;
            $objVacaciones->fecha_fin         = $request->fecha_fin;
            $objVacaciones->cantidad_dias     = $request->cant_dias;
            $objVacaciones->dias_entre_semana = $request->entre_semana;
            $objVacaciones->dias_fines_semana = $request->fin_semana;
            /* $objVacaciones->periodo_desde  = $request->periodo_desde;
            $objVacaciones->periodo_hasta     = $request->periodo_hasta; */
            $objVacaciones->estado            = 0;

            if($objVacaciones->save()){

                $msg .= '<div class="alert alert-success" role="alert" style="margin-bottom: 10px">
                               Las vacaciones han sido enviadas con exito
                           </div>';
                $status = 1;

                Mail::to(getConfiguracionEmpresa()->correo_empresa)->send(new SolicitudVacaciones(getPerson(session('dataUsuario')['id_empleado']),$request->fecha_inicio,$request->fecha_fin));

            }else{

                $msg .= '<div class="alert alert-danger" role="alert" style="margin-bottom: 10px">
                              Ocurrió un error al intentar enviar las vacaciones, intente nuevamente
                           </div>';
                $status = 0;
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
    public function show()
    {
        return  Vacaciones::where('id_empleado',session('dataUsuario')['id_empleado'])
            ->where('estado',1)
           /* ->orWhere('estado',0)*/->count();
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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

    }

    public function diasVacaciones(){

        return [
            ConfiguracionVariablesEmpresa::select('vacaciones_dias_entre_semana','vacaciones_dias_fines_semana')->first(),
            Vacaciones::where('id_empleado',session('dataUsuario')['id_empleado'])->orderBy('id_vacaciones','Desc')->first(),
            Contrataciones::where([
                ['contrataciones.id_empleado',session('dataUsuario')['id_empleado']],
                ['contrataciones.estado',1],
                ['contrataciones.id_tipo_contrato_descripcion',2]
            ])->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')->first()
        ];

    }

    public function deleteVacaciones(Request $request){

        $DeleteVacaciones = Vacaciones::destroy($request->id_vacaciones);
        if($DeleteVacaciones){
            $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                     <i class="fa fa-check-circle" aria-hidden="true"></i>
                     La solicitud de vacaciones ha sido eliminada con éxito
                   </div>';
            $success = true;
        }else{
            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                    <i class="fa fa-window-close" aria-hidden="true"></i>
                     Hubo un error al eliminar la solicitud de vacaciones, intente nuevamente
                   </div>';
            $success = false;
        }
        return [
            'msg' => $msg,
            'success'=> $success
        ];
    }

}
