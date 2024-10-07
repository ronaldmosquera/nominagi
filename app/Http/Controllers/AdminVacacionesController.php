<?php

namespace App\Http\Controllers;

use App\Models\DetalleContratacion;
use Illuminate\Http\Request;
use App\Models\Vacaciones;
use App\Models\Person;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailVacaciones;
use App\Models\ForeginContrataciones;
use App\Models\ConfiguracionVariablesEmpresa;
use App\Models\Contrataciones;
use Carbon\Carbon;
use Validator;

class AdminVacacionesController extends Controller
{

    public function adminVacaciones(Request $request){

        $data= Vacaciones::join('contrataciones as c','vacaciones.id_empleado','c.id_empleado')
            ->where([
                ['c.id_tipo_contrato_descripcion',2],
                ['c.estado',1]
            ]);

        if(!empty($request->desde) && !empty($request->hasta))
            $data->whereBetween('fecha_inicio',[$request->desde,$request->hasta]);

        if(!empty($request->estado))
            $data->where('vacaciones.estado',$request->estado);

        if(!empty($request->id_empleado))
            $data->where('id_empleado',$request->id_empleado);

        $a = $data->orderBy('id_vacaciones','Desc')
            ->where('vacaciones.estado',empty($request->estado) ? 0 : $request->estado)
            ->select('id_vacaciones','vacaciones.id_empleado','vacaciones.fecha_inicio','vacaciones.fecha_fin','vacaciones.cantidad_dias','vacaciones.dias_entre_semana','vacaciones.dias_fines_semana','vacaciones.estado')
            ->get();

        $arrData = [];


        for($i=0;$i<count($a);$i++){

            $b = Person::where('party_id',$a[$i]->id_empleado)->first();
            $arrData[] = [
                'id_vacaciones'     => $a[$i]->id_vacaciones,
                'id_empleado'       => $a[$i]->id_empleado,
                'fecha_inicio'      => $a[$i]->fecha_inicio,
                'fecha_fin'         => $a[$i]->fecha_fin,
                'cant_dias'         => $a[$i]->cantidad_dias,
                'dias_entre_semana' => $a[$i]->dias_entre_semana,
                'dias_fin_semana'   => $a[$i]->dias_fines_semana,
                'estado'            => $a[$i]->estado,
                'nombre'            => $b->first_name." ".$b->last_name
            ];
        }

        return view('layouts.views.vacaciones.admin.list',
            [
                'dataVacaciones' => manualPagination($arrData,10),
                'dataEmpleados'   => ForeginContrataciones::join('person as p','contrataciones.party_id','p.party_id')
                    ->where('contrataciones.estado',1)->get()
            ]);
    }

    public function formComentarioVacacionesNoAprobadas(Request $request){
        return view('layouts.views.vacaciones.admin.form_vacaciones_no_aprobadas',[
            'idVacaciones' => Vacaciones::where('id_vacaciones',$request->id_vacaciones)->select('comentarios','id_vacaciones','id_empleado')->first()
        ]);
    }

    public function storeComentarioVacacionesNoAprobadas(Request $request){

        $valida =  Validator::make($request->all(), [
            'comentario' => 'required',
            'id_vacaciones' => 'required'
        ]);

        if(!$valida->fails()) {

            $objVacaciones = Vacaciones::find($request->id_vacaciones);
            $objVacaciones->comentarios = $request->comentario;
            $objVacaciones->estado = 2;

            if($objVacaciones->save()){

                $msg = '<div class="alert alert-success" role="alert" style="margin-bottom: 10px">
                              El comentario ha sido enviado y se ha rechazado las vacaciones seleccionadas, de igual forma se ha enviado un correo electrónico al empleado para notificarlo
                          </div>';

                $dataVacaciones = Vacaciones::where('id_vacaciones',$request->id_vacaciones)->first();

                $status = 1;
                $message1 = $request->comentario;
                $desde='';
                $hasta='';
                $reincorporacion='';
                $estado=0;

                $mailEmpleado = getMailEmpleado($dataVacaciones->id_empleado);
                if(isset($mailEmpleado))
                    Mail::to(trim($mailEmpleado))->send(new MailVacaciones($estado,$desde,$hasta,$reincorporacion,$message1,$mailEmpleado));

            }else{

                $msg = '<div class="alert alert-danger" role="alert" style="margin-bottom: 10px">
                              Ocurrió un error al intentar enviar el comentario, intente nuevamente
                           </div>';
                $status = 0;
            }

        }else {
            $msg='';
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

    public function update(Request $request)
    {

        $msg = '';
        $aprobadas = $request->arrIdVacacionesAprobadas;

        foreach ($aprobadas as $key => $aprob){

            $dataVacaciones = Vacaciones::find($aprob[0]);
            $estadoVacaciones = Vacaciones::where('id_vacaciones',$aprob[0])->select('estado','fecha_inicio','cantidad_dias')->first();


            $dataVacaciones->estado = $estadoVacaciones->estado == 0 ? 1 : 0;

            if($dataVacaciones->update()){
                $msg .= '<div class="alert alert-success" role="alert" style="margin-bottom: 10px">
                              Se han aprobado las vacaciones seleccionadas, y se ha enviado un correo electrónico al empleado para notificarlo
                          </div>';
                $status = 1;

                $dataContratacion = Contrataciones::where([
                    ['contrataciones.id_empleado',$aprob[1]],
                    ['id_tipo_contrato_descripcion',2],
                    ['estado',1]
                ])->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')->select('contrataciones.id_contrataciones','vacaciones')->first();

                $objDetalleContratacion = DetalleContratacion::find($dataContratacion->id_contrataciones);
                $objDetalleContratacion->vacaciones = $dataContratacion->vacaciones - $estadoVacaciones->cantidad_dias;
                $objDetalleContratacion->save();

                $dataVacaciones = Vacaciones::where('id_vacaciones',$aprob[0])->first();
                $desde = Carbon::parse($dataVacaciones->fecha_inicio)->format('d-m-Y');
                $hasta = Carbon::parse($dataVacaciones->fecha_fin)->format('d-m-Y');
                $reincorporacion = Carbon::parse($dataVacaciones->fecha_fin)->addDay(1)->format('d-m-Y');

                Mail::to(getMailEmpleado($dataVacaciones->id_empleado))->send(new mailvacaciones($status,$desde,$hasta,$reincorporacion,$message= '',getMailEmpleado($dataVacaciones->id_empleado)));

            }else{
                $msg .= '<div class="alert alert-danger" role="alert" style="margin-bottom: 10px">
                                No pudieron ser aprobadas las vacaciones seleccionadas, intente nuevamente
                            </div>';
                $status = 0;
            }
        }

        return response()->json(['status'=>$status,'msg'=>$msg]);
    }

    public function editVacaciones(Request $request)
    {
        return view('layouts.views.vacaciones.admin.form_vacaciones',
            [
                'dataVacaciones' => Vacaciones::where('id_vacaciones',$request->id_vacaciones)->first(),
                //'id_horas_extras' => $request->id_horas_extras
            ]);
    }

    public function storeEditVacaciones(Request $request)
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

            $objVacaciones = Vacaciones::find($request->id_vacacion);
            $objVacaciones->fecha_inicio      = $request->fecha_inicio;
            $objVacaciones->fecha_fin         = $request->fecha_fin;
            $objVacaciones->cantidad_dias     = $request->cant_dias;
            $objVacaciones->dias_entre_semana = $request->entre_semana;
            $objVacaciones->dias_fines_semana = $request->fin_semana;
            /* $objVacaciones->periodo_desde     = $request->periodo_desde;
            $objVacaciones->periodo_hasta     = $request->periodo_hasta; */
            $objVacaciones->estado            = 0;

            if($objVacaciones->save()){

                $msg .= '<div class="alert alert-success" role="alert" style="margin-bottom: 10px">
                               Las vacaciones han sido editadas con exito
                           </div>';
                $status = 1;

            }else{

                $msg .= '<div class="alert alert-danger" role="alert" style="margin-bottom: 10px">
                              Ocurrió un error al intentar editar las vacaciones, intente nuevamente
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

    public function diasVacacionesAdmin(Request $request){

        return [
            ConfiguracionVariablesEmpresa::select('vacaciones_dias_entre_semana','vacaciones_dias_fines_semana')->first(),
            Vacaciones::where('id_empleado',$request->id_empleado)->orderBy('id_vacaciones','Desc')->first(),
            Contrataciones::where([
                ['contrataciones.id_empleado',$request->id_empleado],
                ['contrataciones.estado',1],
                ['contrataciones.id_tipo_contrato_descripcion',2]
            ])->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')->select('vacaciones')->first()
        ];

    }

}

