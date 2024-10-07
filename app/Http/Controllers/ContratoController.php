<?php

namespace App\Http\Controllers;

use App\Models\Contrataciones;
use Illuminate\Http\Request;
use App\Models\TipoContratos;
use App\Models\Contrato;
use Validator;
use DB;

class ContratoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('layouts.views.contrato.list',
            [
                'dataContratos' => Contrato::join('tipo_contrato as tc','contrato.id_tipo_contrato','=','tc.id_tipo_contrato')
                    ->join('tipo_contrato_descripcion','tc.id_tipo_contrato_descripcion','=','tipo_contrato_descripcion.id_tipo_contrato_descripcion')
                    ->select('tc.id_tipo_contrato','contrato.estado as cestado','contrato.id_contrato','tipo_contrato_descripcion.descripcion_tipo_contrato','contrato.descripcion_contrato','tc.nombre')
                    ->where('contrato.estado',true)->get()
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dataTipoContratos = TipoContratos::all()->count();

        if($dataTipoContratos == 0){
            flash('<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Se deben agregar tipos de contratos para poder crear contratos</div>')->error()->important();
            return view('layouts.views.contrato.list',['dataContratos'=>[]]);
        }else{

            $dataTipoContratos = $dataTipoContratos = TipoContratos::where('estado',1)
                ->whereNotIn('id_tipo_contrato', function ($query){
                    $query->select('id_tipo_contrato')->from('contrato');
                })->get();

            return view('layouts.views.contrato.partials.form_contrato',
                [
                    'dataTipoContratos' => $dataTipoContratos
                ]);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       //dd($request->all());

        $valida =  Validator::make($request->all(), [
            'id_tipo_contrato' => 'required',
            'body_contrato' => 'required',
        ]);

        $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                      hubo un error al guardar el contrato, intente nuevamente
                </div>';
        $status = 0;

        if(!$valida->fails()) {

            empty($request->id_contrato) ? $objContrato = new Contrato:  $objContrato = Contrato::find($request->id_contrato);
            $objContrato->id_tipo_contrato = $request->id_tipo_contrato;
            $objContrato->cuerpo_contrato  = $request->body_contrato;

            if($objContrato->save()){
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      El contrato se ha guardado con exito!
                </div>';
                $status = 1;
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
        $dataContrato = Contrato::find($id);
        //dd($dataContrato);

        $dataTipoContratos = TipoContratos::where('estado',1)->get();
        return view('layouts.views.contrato.partials.form_contrato',
            [
                'dataTipoContratos' => $dataTipoContratos,
                'dataContrato'  =>$dataContrato
            ]);
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
        /* if(getExistContrato($request->id_contrato)) {

            $msg = '<div class="alert alert-info" role="alert" style="margin: 0">
                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                    El contrato no puede ser desactivado por que está en uso
                    </div>';
        }else{ */
            $objContrato = Contrato::find($request->id_contrato);

            $objContrato->estado = $request->estado == 1 ? 0 : 1;

            if($objContrato->save()){
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      Se ha actualizado el estatus del contrato con éxito
                    </div>';
            }else{
                $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                      Hubo un error al eliminar el contrato, intente nuevamente
                    </div>';
            }
       // }
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

    public function deleteContrato(Request $request)
    {
        if(getExistContrato($request->id_contrato)){
            $msg = '<div class="alert alert-info" role="alert" style="margin: 0">
                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                    El contrato no puede ser eliminado por que está en uso
                    </div>';
        }else{
            $dataContrato = Contrato::destroy($request->id_contrato);
            if($dataContrato == 1){
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
        }

        return $msg;
    }


}
