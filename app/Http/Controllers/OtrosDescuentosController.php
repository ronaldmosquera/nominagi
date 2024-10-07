<?php

namespace App\Http\Controllers;

use App\Mail\MailDescuentos;
use Illuminate\Http\Request;
use App\Models\OtrosDescuentos;
use App\Models\ForeginContrataciones;
use App\Models\InvoiceItemType;
use App\Models\Person;
use Illuminate\Support\Facades\Mail;
use PhpXmlRpc\Value;
use PhpXmlRpc\Request as ResqClientXmlrpc;
use PhpXmlRpc\Client;
use Validator;

class OtrosDescuentosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = OtrosDescuentos::where(function($q) use ($request){
            if(!empty($request->estado))
                $q->where('descontado',$request->estado);

            if(!empty($request->id_empleado))
                $q->where('id_empleado',$request->id_empleado);
        })->orderBy('id_descuento','desc')->paginate(10);

        /*$a = $data->orderBy('id_descuento','Desc')->get();

        $arrData = [];

        for($i=0;$i<count($a);$i++){

            $b = Person::where('party_id',$a[$i]->id_empleado)->first();

            $arrData[] = [
                'id_descuento'    => $a[$i]->id_descuento,
                'id_empleado'     => $a[$i]->id_empleado,
                'fecha_descuento' => $a[$i]->fecha_descuento,
                'cantidad'        => $a[$i]->cantidad,
                'descripcion'     => $a[$i]->descripcion,
                'descontado'      => $a[$i]->descontado,
                'nombre'          => $b->first_name." ".$b->last_name
            ];
        } */

        //dd($arrData);
        return view('layouts.views.descuentos.admin.list',
            [
                'dataDescuentos' => $data,
                //'dataDescuentos' => manualPagination($arrData,10),
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
        return view('layouts.views.descuentos.partials.form_add_descuentos',[
            'dataDescuentos' => OtrosDescuentos::where('id_descuento',$request->id_descuento)->first()
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
        $valida = Validator::make($request->all(), [
            'arrData' => 'required|Array'
        ]);

        $msg = '';
        $status = 0;
        if (!$valida->fails()) {

            foreach ($request->arrData as $data) {

                $objDescuento = empty($request->id_descuento)
                    ? new OtrosDescuentos
                    : OtrosDescuentos::find($request->id_descuento);

                $objDescuento->id_empleado      = $data[3];
                $objDescuento->fecha_descuento  = $data[1];
                $objDescuento->cantidad         = $data[2];
                $objDescuento->descripcion      = $data[4];
                $objDescuento->nombre           = $data[5];
                $objDescuento->invoice_item_type_id =$data[6];
                $objDescuento->persona           = $data[7];
                if ($objDescuento->save()) {

                    Mail::to(trim(getMailEmpleado($objDescuento->id_empleado)))
                            ->send(new MailDescuentos(trim(getMailEmpleado($objDescuento->id_empleado)),$data[2],$data[1],$data[4]));
                    $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                               Descuentos asignados con exito!
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

    }

    public function deleteDescuento(Request $request){
        if(OtrosDescuentos::where('id_descuento',$request->id_descuento)->delete()){

            $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                        El descuento se ha eliminado con éxito
                    </div>';
        }else{
            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                        Hubo un error al eliminar el descuento, intente nuevamente
                   </div>';
        }
        return $msg;
    }


    public function addInputs(Request $request){

        return view('layouts.views.descuentos.partials.inputs_descuentos',
            [
                'cant_input'=> $request->cant_inputs,
                'dataEmpleados'   => ForeginContrataciones::join('person as p','contrataciones.party_id','p.party_id')
                    ->where('contrataciones.estado',1)->select('first_name','last_name','p.party_id')->distinct()->get(),
                'dataDescuentos' => OtrosDescuentos::where('descontado',1)->get(),
                'conceptosDescuento' => InvoiceItemType::where('parent_type_id','NOMINA_DESC_PURCHASE')->get()
            ]);
    }

    public function getConceptoDescuentos(Request $request){

        if(getContratacionByEmpleado($request->id_empleado)->tipo_contratacion->relacion_dependencia){
            $InvoiceItemType =InvoiceItemType::where([
                ['parent_type_id', 'NOMINA_DESC_PAYROL'],
                ['activo', 'S']
            ])->select('invoice_item_type_id','description')->get();
        }else{
            $InvoiceItemType =InvoiceItemType::where([
                ['parent_type_id', 'NOMINA_DESC_PURCHASE'],
                ['activo','S']
            ])->select('invoice_item_type_id','description')->get();
        }

        return $InvoiceItemType;
    }


}
