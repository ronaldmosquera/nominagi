<?php

namespace App\Http\Controllers;

use App\Mail\MailConsumos;
use App\Models\Consumos;
use App\Models\DetalleConsumo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Productos;
use App\Models\ConfiguracionVariablesEmpresa;
use App\Models\Person;
use Illuminate\Support\Facades\Mail;
use App\Models\Invoice;
use Validator;
use DB;

class ConsumosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dataConsumo = Consumos::where('id_empleado',session('dataUsuario')['id_empleado'])
        ->where('estado',isset($request->estado) ? $request->estado : 0);


        if($request->fecha != '' && $request->fecha != null)
            $dataConsumo = $dataConsumo->where('fecha_descuento',Carbon::parse($request->fecha)->format('Y-m-05'));

        return view('layouts.views.consumos.list',[
            'data_consumo' => $dataConsumo->paginate(10)

            /*DB::connection(getConnection(0))->select('select invoice_id, invoice_date, invoice_number, (sub_total_imp1+sub_total_imp2 + total_iva) as total ,(CASE WHEN (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) IS NOT NULL THEN
            (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) ELSE 0 END)  as pagado ,(sub_total_imp1+sub_total_imp2 + total_iva) - (CASE WHEN (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) IS NOT NULL THEN  
            (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) ELSE 0 END) as saldo from invoice where invoice_type_id=\'SALES_INVOICE\' AND status_id=\'INVOICE_READY\' AND party_id=\''.session('dataUsuario')['id_empleado'].'\'')*/
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('layouts.views.consumos.partials.form_consumo',[

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
        /*$valida = Validator::make($request->all(), [
                'arr_data_consumo' => 'required|Array',
                'total' => 'required',
                'meses_diferir' => 'required',
                'fecha_inicio_diferir' => 'required',
            ]);

            $msg = '';
            $status = 0;
            if (!$valida->fails()) {

                empty($request->id_consumo) ? $objConsumo = new Consumos : $objConsumo = Consumos::find($request->id_consumo);

                $objConsumo->id_empleado = session('dataUsuario')['id_empleado'];
                $objConsumo->fecha_solicitud = \Carbon\Carbon::now()->format('Y-m-d');
                $objConsumo->total = $request->total;
                $objConsumo->meses_diferir = $request->meses_diferir;
                $objConsumo->fecha_inicio_diferir = $request->fecha_inicio_diferir;

                if ($objConsumo->save()) {

                    $model = Consumos::all()->last();

                    if (!empty($request->id_consumo))
                        DetalleConsumo::where('id_consumo', $request->id_consumo)->delete();

                    foreach ($request->arr_data_consumo as $dataConsumo) {

                        $objDetalleConsumo = new DetalleConsumo;
                        $objDetalleConsumo->id_consumo = $model->id_consumo;
                        $objDetalleConsumo->cantidad = $dataConsumo[0];
                        $objDetalleConsumo->id_producto = $dataConsumo[1];

                        if ($objDetalleConsumo->save()) {
                            $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                                       Solicitud enviada con exito!
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
                    $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                          Hubo un inconveniente al guardar los datos, intente nuevamente
                    </div>';
                    $status = 0;
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
        return response()->json(['status'=>$status,'msg'=>$msg]);*/
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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        /*if(DetalleConsumo::where('id_consumo',$request->id_consumo)->delete()){

            if(Consumos::destroy($request->id_consumo)){
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                         El consumo se ha eliminado con éxito
                        </div>';
            }else{
                $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                          Hubo un error al eliminar el consumo, intente nuevamente
                        </div>';
            }
        }else{
            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                          Hubo un error al eliminar el consumo, intente nuevamente
                        </div>';
        }

        return $msg;*/
    }

    public function addInputs(Request $request){

        /*return view('layouts.views.consumos.partials.inputs_consumo',
            [
                'dataProductos' => Productos::where('estado',1)->get(),
                'cant_input'=> $request->cant_input,
            ]);*/
    }

}
