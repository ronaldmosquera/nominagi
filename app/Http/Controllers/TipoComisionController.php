<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoComision;
use Validator;

class TipoComisionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('layouts.views.tipo_comisiones.list',[
            'dataTipoComision' => TipoComision::orderBy('id_tipo_comision','Desc')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('layouts.views.tipo_comisiones.partials.form_add_tipo_comision',
            [
                'tipoComision' => TipoComision::where('id_tipo_comision',$request->id_tipo_comision)->first()
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
            'nombre'         => 'required',
            'monto_estandar' => 'required',
            'descripcion'    => 'required',
            'decimo_tercero' => 'required'
        ]);

        $msg='';
        if(!$valida->fails()) {

            empty($request->id_tipo_comision) ? $objTipoComision = new TipoComision : $objTipoComision = TipoComision::find($request->id_tipo_comision);

            $objTipoComision->nombre                  = $request->nombre;
            $objTipoComision->estandar                = $request->monto_estandar;
            $objTipoComision->descripcion             = $request->descripcion;
            $objTipoComision->calculo_decimo_tercero  = $request->decimo_tercero;

            if ($objTipoComision->save()) {
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      Tipo de comisión agregado con éxito
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
        $objTipoComision = TipoComision::find($request->id_tipo_comision);

        $objTipoComision->estado = $request->estado == 1 ? 0 : 1;

        if($objTipoComision->save()){
            $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      Se ha actualizado el estatus del tipo de comsión con éxito
                    </div>';
        }else{
            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                      Hubo un error al actualizar el estado del tipo de comsión, intente nuevamente
                    </div>';
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

        if(TipoComision::destroy($request->id_tipo_comision)){

            $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                          Tipo de comisión Eliminado con éxito
                        </div>';
        }else{
            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                          Hubo un error al eliminar el tipo de comisión, intente nuevamente
                        </div>';
        }

        return $msg;
    }
}
