<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use Illuminate\Http\Request;
use App\Models\TipoContratos;
use App\Models\TipoContratoDescripcion;

use Validator;
use DB;

class TipoContratoController extends Controller
{
    /**
     * Display a listing of the resource.
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('layouts.views.tipo_contrato.list',
            [
                'dataTipoContratos'=>
                    TipoContratos::join('tipo_contrato_descripcion as tcd','tipo_contrato.id_tipo_contrato_descripcion','=','tcd.id_tipo_contrato_descripcion')
                        ->where('estado',true)->orderBy('nombre','asc')
                        ->paginate(10)
            ]);
            }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
            'nombre'                       => 'required',
            'horas_extras'                 => 'required',
            'descripcion'                  => 'required',
            'relacion_dependencia'         => 'required',
            'id_tipo_contrato_descripcion' => 'required',
            'horas_extras'                 => 'required',
            'relacion_dependencia'         => 'required',
            'caducidad'                    => 'required',
            'sueldo_sectorial'                => 'required'
        ]);

        $msg='';
        if(!$valida->fails()) {

            empty($request->id_tipo_contrato) ? $objTipoContrato = new TipoContratos : $objTipoContrato = TipoContratos::find($request->id_tipo_contrato);

            $objTipoContrato->nombre                       = $request->nombre;
            $objTipoContrato->descripcion                  = $request->descripcion;
            $objTipoContrato->descripcion                  = $request->descripcion;
            $objTipoContrato->id_tipo_contrato_descripcion = $request->id_tipo_contrato_descripcion;
            $objTipoContrato->horas_extras                 = $request->horas_extras;
            $objTipoContrato->relacion_dependencia         = $request->relacion_dependencia;
            $objTipoContrato->caducidad                    = $request->caducidad;
            $objTipoContrato->sueldo_sectorial             = $request->sueldo_sectorial;

            if ($objTipoContrato->save()) {
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      Registro agregado con éxito
                </div>';
                $status = 1;

            } else{
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

    public function updateStatus(Request $request)
    {
        $countContrato = Contrato::where('id_tipo_contrato',$request->id)->count();

        if($countContrato > 0 ) {
            $msg = '<div class="alert alert-info" role="alert" style="margin: 0">
                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                    El tipo de contrato no puede ser desactivado por que está en uso
                    </div>';
        }else{
            $objTipoContrato = TipoContratos::find($request->id);

            $objTipoContrato->estado = $request->estado == 1 ? 0 : 1;

            if($objTipoContrato->save()){
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      Se ha actualizado el estatus de Tipo de contrato con éxito
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

    }

    public function deleteTipoContrato(Request $request)
    {
        $countContrato = Contrato::where('id_tipo_contrato',$request->id)->count();

        if($countContrato > 0 ){
            $msg = '<div class="alert alert-info" role="alert" style="margin: 0">
                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                    El tipo de contrato no puede ser eliminado por que está en uso
                    </div>';
        }else{
            $dataTipoContrato = TipoContratos::destroy($request->id);
            if($dataTipoContrato == 1){
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      <i class="fa fa-check-circle" aria-hidden="true"></i>
                      El tipo de contrato ha sido eliminado con éxito
                    </div>';
            }else{
                $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                     <i class="fa fa-window-close" aria-hidden="true"></i>
                      Hubo un error al eliminar el tipo de contrato, intente nuevamente
                    </div>';
            }
        }
        return $msg;
    }

    public function vistaFormContrato(Request $request){

        if(!empty($request->id_tipo_contrato)){
            $dataTipoContrato = TipoContratos::find($request->id_tipo_contrato);
        }else{
            $dataTipoContrato ='';
        }

        return view('layouts.views.tipo_contrato.partials.form_tipo_contrato',[
            'dataTipoContrato' => $dataTipoContrato,
            'datTipocontratoDescripcion' => TipoContratoDescripcion::all()
        ]);
    }
}
