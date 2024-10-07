<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Iva;
use Validator;

class IvaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       return view('layouts.views.iva.list',[
          'dataIva' =>Iva::get()
       ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('layouts.views.iva.partials.form_add_iva',[
            'iva' => Iva::where('id_iva',$request->id_iva)->first()

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
            'nombre' => 'required',
            'valor' => 'required'
        ]);

        $status=0;
        $msg='';
        if(!$valida->fails()) {

            empty($request->id_iva) ? $objIva = new Iva : $objIva = Iva::find($request->id_iva);

            $objIva->nombre = $request->nombre;
            $objIva->valor  = $request->valor;

            if($objIva->save()){
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      El iva se ha guardado con éxito
                </div>';
                $status = 1;
            }else{
                $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                      Hubo un error al guardar el iva, intente nuevamente
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
        if(Iva::destroy($request->id_iva)){
            $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                          Se ha eliminado el iva con éxito
                        </div>';
        }else{
            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                          Hubo un error al eliminar el iva, intente nuevamente
                        </div>';
        }
        return $msg;
    }
}
