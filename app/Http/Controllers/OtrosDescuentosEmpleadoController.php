<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OtrosDescuentos;

class OtrosDescuentosEmpleadoController extends Controller
{
    public function listDescuentos(Request $request){

        $data = OtrosDescuentos::where('id_empleado',session('dataUsuario')['id_empleado']);

        if(!empty($request->estado))
            $data->where('descontado',$request->estado);

        return view('layouts.views.descuentos.list',[
            'dataDescuentos' => $data->orderBy('id_descuento','Desc')->paginate(10),
        ]);
    }
}
