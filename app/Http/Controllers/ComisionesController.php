<?php

namespace App\Http\Controllers;

use App\Mail\MailComisiones;
use App\Models\TipoComision;
use Illuminate\Http\Request;
use App\Models\ForeginContrataciones;
use App\Models\Comisiones;
use App\Models\PartyContactMech;
use Illuminate\Support\Facades\Mail;
use App\Models\Person;
use Validator;

class ComisionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $data= Comisiones::where(function($q) use ($request){

            if(!empty($request->fecha))
                $q->where('fecha_nomina',$request->fecha);

            if(!empty($request->id_empleado))
                $q->where('id_empleado',$request->id_empleado);

        })->orderBy('id_comisiones','Desc')->get();

        $arrData = [];

        for($i=0;$i<$data->count();$i++){

            $b = Person::where('party_id',$data[$i]->id_empleado)->first();

            $arrData[] = [
                'id_comision'     => $data[$i]->id_comisiones,
                'id_empleado'     => $data[$i]->id_empleado,
                'fecha_nomina'    => $data[$i]->fecha_nomina,
                'cantidad'        => $data[$i]->cantidad,
                'concepto'        => getConceptoComision($data[$i]->id_comisiones)->nombre,
                'descripcion'     => $data[$i]->descripcion,
                'esatdo'          => $data[$i]->estado,
                'nombre'          => $b->first_name." ".$b->last_name
            ];
        }

       return view('layouts.views.comisiones.admin.list',
           [
               'dataComisiones' => manualPagination($arrData,10),
               'dataEmpleados'   => ForeginContrataciones::join('person as p','contrataciones.party_id','p.party_id')
                   ->where('contrataciones.estado',1)->select('first_name','last_name','p.party_id')->distinct()->get()
           ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('layouts.views.comisiones.partials.form_add_comision',[
            'dataComision' => Comisiones::where('id_comisiones',$request->id_comision)->first()
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
        //dd($request->all());
        $valida = Validator::make($request->all(), [
            'arrData' => 'required|Array'
        ]);

        $msg = '';
        $status = 0;
        if (!$valida->fails()) {

            foreach ($request->arrData as $data) {

                empty($request->id_comision) ? $objComision = new Comisiones : $objComision = Comisiones::find($request->id_comision);
                $objComision->id_empleado      = $data[0];
                $objComision->fecha_nomina     = $data[1];
                $objComision->cantidad         = $data[2];
                $objComision->id_tipo_comision = $data[4];
                $objComision->descripcion      = $data[3];

                $dataComision = TipoComision::where('id_tipo_comision',$data[4])->select('nombre','estandar')->first();

                if ($objComision->save()) {

                    Mail::to(getMailEmpleado($objComision->id_empleado))->send(new MailComisiones(getMailEmpleado($objComision->id_empleado),$data[2],$data[1],$data[3],$dataComision->nombre));
                    $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                               Comisiones asignadas con exito!
                            </div>';
                    $status = 1;
                } else {
                    $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                               Hubo un inconveniente al guardar los datos, intente nuevamente
                            </div>';
                    $status = 0;
                }
            }
        } else {
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
    public function show(Request $request)
    {

        return TipoComision::where('id_tipo_comision',$request->id_tipo_contrato)->select('estandar')->first();
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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if(Comisiones::destroy($request->id_comision)){
            $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                          Se ha eliminado la comisión con éxito
                        </div>';
        }else{
            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                          Hubo un error al eliminar la comision, intente nuevamente
                        </div>';
        }
        return $msg;
    }

    public function addInputs(Request $request){

        return view('layouts.views.comisiones.partials.inputs_comisiones',
            [
               //'dataProductos' => Productos::where('estado',1)->get(),
                'cant_input'=> $request->cant_inputs,
                'dataEmpleados'   => ForeginContrataciones::join('person as p','contrataciones.party_id','p.party_id')
                    ->where('contrataciones.estado',1)->select('first_name','last_name','p.party_id')->distinct()->get(),
                'dataComisiones' => TipoComision::where('estado',1)->get()
            ]);
    }


}
