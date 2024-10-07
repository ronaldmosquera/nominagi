<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contrataciones;
use App\Models\ImagenesDetallesContrataciones;

class ContratacionesEmpleadoController extends Controller
{
    public function index(Request $request){

        $dataContrato = Contrataciones::where('contrataciones.id_empleado',session('dataUsuario')['id_empleado'])
        ->where('contrataciones.estado', !empty($request->estado) ? $request->estado : 1);

        if($request->tipo_contrato!=null)
            $dataContrato->where('contrataciones.id_tipo_contrato_descripcion',$request->tipo_contrato);

        if($request->estado == 3)
            $dataContrato->join('finalizacion_contrataciones as fc', 'contrataciones.id_contrataciones','fc.id_contrataciones');

        $dataContrato = $dataContrato->orderBy('contrataciones.id_contrataciones', 'Desc')
            ->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','=','dc.id_contrataciones')
            ->join('tipo_contrato_descripcion as tcd','contrataciones.id_tipo_contrato_descripcion','tcd.id_tipo_contrato_descripcion')
            ->join('tipo_contrato as tc','contrataciones.id_tipo_contrato','tc.id_tipo_contrato')
            ->select('contrataciones.*','dc.*','tcd.*','tc.nombre','tc.relacion_dependencia','tc.horas_extras', $request->estado == 3 ? 'fc.*' : 'tc.nombre');

        return view('layouts.views.contrataciones.empleado.list',[
            'dataContrataciones' => $dataContrato->paginate(10)
        ]);
    }

    public function addContratacionEmpleado(Request $request){

        $detalleContratacion = Contrataciones::where([
            ['id_empleado',$request->id_empleado],
            ['estado',1],
            ['id_tipo_contrato_descripcion',2]
        ])->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')
            ->select('id_detalle_contrataciones')->first();

        return view('layouts.views.contrataciones.empleado.partials.documentos_firmados',[
            'contratacionesFirmadas'=> ImagenesDetallesContrataciones::where('id_detalles_contrataciones',$detalleContratacion->id_detalle_contrataciones)->get()
        ]);


    }
}
