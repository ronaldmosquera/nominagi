<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anticipos;
use App\Models\ConfiguracionVariablesEmpresa;
use App\Models\Contrataciones;
use App\Mail\MailAnticipos;
use App\Mail\SolicitudAnticipos;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Validator;

class AnticiposController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dataAnticipos = Anticipos::where([
            ['id_empleado',session('dataUsuario')['id_empleado']],
            ['estado', isset($request->estado) ? $request->estado : 0 ]
        ]);

       return view('layouts.views.anticipos.list',
           [
               'dataAnticipo' => $dataAnticipos->paginate(10)
           ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $existAnticipo = Anticipos::where('id_empleado',session('dataUsuario')['id_empleado'])
        ->where('estado','!=',3)->orWhere('estado','!=',2)
        ->orderBy('id_anticipo','desc')->first();

        $contratacion = Contrataciones::join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')
                        ->join('tipo_contrato as tc', 'contrataciones.id_tipo_contrato','tc.id_tipo_contrato')
                        ->where([
                            ['id_empleado',session('dataUsuario')['id_empleado']],
                            ['contrataciones.id_tipo_contrato_descripcion',2],
                            ['contrataciones.estado',1],
                        ])->first();

        $dataConfiguracionEmpresaVariables = ConfiguracionVariablesEmpresa::select('antiguedad','fecha_hasta','intervalo')->first();

        return view('layouts.views.anticipos.partials.form_add_anticipo',[
            'dataAnticipo' => Anticipos::where('id_anticipo',$request->id_anticipo)->first(),
            'mesesContrato' => Carbon::parse($contratacion->fecha_expedicion_contrato)->diffInMonths(now()) >= $dataConfiguracionEmpresaVariables->antiguedad,
            'fechaPlazo' => now()->format('Y-m-d') > now()->format('Y-m-'.$dataConfiguracionEmpresaVariables->fecha_hasta),
            'intervaloSolicitud' => isset($existAnticipo) ? Carbon::parse($existAnticipo->fecha_descuento)->diffInMonths(now()) < $dataConfiguracionEmpresaVariables->intervalo : false,
            'ultimoAnticipo' => $existAnticipo,
            'diaHasta' => $dataConfiguracionEmpresaVariables->fecha_hasta,
            'antiguedad' => $dataConfiguracionEmpresaVariables->antiguedad,
            'intervalo' => $dataConfiguracionEmpresaVariables->intervalo,
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
            'cantidad'        => 'required',
            'fecha_entrega'   => 'required',
            'fecha_descuento' => 'required',
        ]);

        $msg='';
        if(!$valida->fails()) {

            $objAnticipo = empty($request->id_anticipo) ? new Anticipos : Anticipos::find($request->id_anticipo);

            in_array('EMPLOYEE',session('dataUsuario')['user_type']) ? $objAnticipo->id_empleado = session('dataUsuario')['id_empleado'] : '';
            $objAnticipo->cantidad        = $request->cantidad;
            $objAnticipo->fecha_entrega   = $request->fecha_entrega;
            $objAnticipo->fecha_descuento = $request->fecha_descuento;
            $objAnticipo->invoice_item_type_id = getRelacionDependencia(session('dataUsuario')['id_empleado'])->relacion_dependencia ? 'ANTICIPOS_PR' : 'ANTICIPOS_PURCHASE';

            if ($objAnticipo->save()) {
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                            Anticipo solicitado con éxito
                        </div>';
                $status = 1;

                Mail::to(getConfiguracionEmpresa()->correo_empresa)
                        ->send(new SolicitudAnticipos(getPerson(session('dataUsuario')['id_empleado']),$request->fecha_entrega,$request->fecha_descuento, $request->cantidad));

            } else{
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      Hubo un inconveniente al soliciar el anticipo, intente nuevamente
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
    public function show($id)
    {
        return [
            'porcentaje_anticipo' => ConfiguracionVariablesEmpresa::select('porcentaje_avance')->first()->porcentaje_avance,
            'salario_base' => Contrataciones::where([
                ['id_empleado',session('dataUsuario')['id_empleado']],
                ['contrataciones.estado',1],
                ['contrataciones.id_tipo_contrato_descripcion',2]
            ])->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')->select('salario')->first()->salario
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


    public function deleteAnticipo(Request $request){

        $dataAnticipo = Anticipos::destroy($request->id_anticipo);
        if($dataAnticipo){
            $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                          Anticipo Eliminado con éxito
                        </div>';
            $success = true;
        }else{
            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                          Hubo un error al eliminar el anticipo, intente nuevamente
                        </div>';
            $success =false;
        }
        return [
            'msg' => $msg,
            'success' => $success
        ];

    }


    public function actualizarAnticipo(Request $request){
        $msg = '';

        foreach ($request->arrIdAnticiposAprobados as $key => $idAnticipo){

            $dataAnticipo = Anticipos::find($idAnticipo);

            $dataAnticipo->estado = 1;

            if($dataAnticipo->save()){
                $msg .= '<div class="alert alert-success" role="alert" style="margin-bottom: 10px">
                              Se han aprobado los anticipos seleccionados, y se ha enviado un correo electrónico al empleado para notificarlo
                          </div>';
                $status = 1;

                $dataAnticipo = Anticipos::where('id_anticipo',$idAnticipo)->first();

                $message1 = $request->comentario;
                $estado=1;

                Mail::to(getMailEmpleado($dataAnticipo->id_empleado))->send(new MailAnticipos($estado,$message1,getMailEmpleado($dataAnticipo->id_empleado),$dataAnticipo));

            }else{
                $msg .= '<div class="alert alert-danger" role="alert" style="margin-bottom: 10px">
                                No pudieron ser aprobados los anticipos seleccionadas, intente nuevamente
                            </div>';
                $status = 0;
            }
        }
        return response()->json(['status'=>$status,'msg'=>$msg]);
    }
}
