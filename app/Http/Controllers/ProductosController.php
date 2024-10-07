<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Productos;
use Validator;


class ProductosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       //dd($request->estado);

        $data = Productos::where('estado',$request->estado != '' ? $request->estado : 1);

        if($request->nombre_producto != '')
            $data->where('nombre','like','%'.$request->nombre_producto.'%');

        $data = $data->orderBy('id_productos','asc')->paginate(20);

        return view('layouts.views.productos.list',
            [
                'productos'=> $data
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('layouts.views.productos.partials.form_add_productos');
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
            'archivo' => 'required|mimes:xls,xlsx'
        ]);

        $status=0;
        $msg='';
        if(!$valida->fails()) {

            Excel::load($request->file('archivo'), function ($reader) {

                $datos = $reader->get()[0]->toArray();

                foreach ($datos as $key => $row) {

                    $objProducto = new Productos;
                    $objProducto->nombre = $row['producto'];
                    $objProducto->costo  = $row['costo'];
                    $objProducto->save();
                }
            });
            $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      Los productos se han guardado con exito
                </div>';
            $status = 1;

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
        $dataProducto = Productos::find($request->id_producto);
        $dataProducto->estado = $request->estado == 1 ? 0 : 1;

        if($dataProducto->update()){

            $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      El estado se ha actualizado con exito
                </div>';

        }else{
            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                      Hubo un error al actualizar el estado del producto, intente nuevamente
                </div>';

        }
        return response()->json(['msg'=>$msg]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function obtenerCostoProducto(Request $request){

        return Productos::where('id_productos',$request->id_producto)->select('costo')->first();
    }


}
