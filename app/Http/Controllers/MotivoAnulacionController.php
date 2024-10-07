<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MotivoAnulacion;
use App\Models\FinalizacionContratacion;
use Validator;

class MotivoAnulacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('layouts.views.motivo_anulaciones.list',['dataMotivosAnulacionContrato'=> MotivoAnulacion::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('layouts.views.motivo_anulaciones.partials.form_motivo_anulaciones',
            ['dataMotivoAnulacion'=>MotivoAnulacion::find($request->id_motivo_anulacion)]);
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
            'nombre'       => 'required',
            'descripcion'  => 'required',
            'calculo_deshaucio' => 'required',
            'calculo_despido_intempestivo' => 'required',
            'calcula_liquidacion' => 'required'
        ]);

        $msg='';
        if(!$valida->fails()) {

            empty($request->id_motivo_anulacion) ? $obMotivoAnulacion = new MotivoAnulacion : $obMotivoAnulacion = MotivoAnulacion::find($request->id_motivo_anulacion);

            $obMotivoAnulacion->nombre               = $request->nombre;
            $obMotivoAnulacion->descripcion          = $request->descripcion;
            $obMotivoAnulacion->desahucio            = $request->calculo_deshaucio;
            $obMotivoAnulacion->despido_intempestivo = $request->calculo_despido_intempestivo;
            $obMotivoAnulacion->calcula_liquidacion  = $request->calcula_liquidacion;

            if ($obMotivoAnulacion->save()) {
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      Registro agregado con éxito
                </div>';
                $status = 1;

            }else{
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      Hubo un inconveniente al guardar los datos, intente nuevamente
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
        //
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
        $existMotivoAnulacion = FinalizacionContratacion::where('id_tipo_finalizacion',$request->id_motivo_anulacion)->count();

        if($existMotivoAnulacion > 0) {
            $msg = '<div class="alert alert-info" role="alert" style="margin: 0">
                    <i class="fa fa-info-circle" aria-hidden="true"></i>  
                    El registro no puede ser desactivado por que está en uso
                    </div>';
        }else{
            $objMotivoAnulacion = MotivoAnulacion::find($request->id_motivo_anulacion);

            $objMotivoAnulacion->estado = $request->estado == 1 ? 0 : 1;

            if($objMotivoAnulacion->save()){
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      Se ha actualizado el registro con éxito
                    </div>';
            }else{
                $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                      Hubo un error al eliminar el registro, intente nuevamente
                    </div>';
            }
        }
        return $msg;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $existMotivoAnulacion = FinalizacionContratacion::where('id_tipo_finalizacion',$request->id_motivo_anulacion)->count();

        if($existMotivoAnulacion > 0 ){
            $msg = '<div class="alert alert-info" role="alert" style="margin: 0">
                    <i class="fa fa-info-circle" aria-hidden="true"></i>  
                    El registro no puede ser eliminado por que está en uso
                    </div>';
        }else{
            $dataMotivoAnulacion = MotivoAnulacion::destroy($request->id_motivo_anulacion);
            if($dataMotivoAnulacion == 1){
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      <i class="fa fa-check-circle" aria-hidden="true"></i>
                      El registro ha sido eliminado con éxito
                    </div>';
            }else{
                $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                     <i class="fa fa-window-close" aria-hidden="true"></i>
                      Hubo un error al eliminar el registro, intente nuevamente
                    </div>';
            }
        }
        return $msg;
    }
}
