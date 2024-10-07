<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ForeginContrataciones;
use App\Models\Consumos;
use Validator;
use DB;

class AdminConsumosController extends Controller
{
    public function adminConsumos(Request $request){

        $arrData = DB::connection(getConnection(0))->select('select invoice_id, invoice_date, invoice_number, (sub_total_imp1+sub_total_imp2 + total_iva) as total , (CASE WHEN (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) IS NOT NULL THEN
            (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) ELSE 0 END)  as pagado, (sub_total_imp1+sub_total_imp2 + total_iva) - (CASE WHEN (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) IS NOT NULL THEN  
            (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) ELSE 0 END) as saldo, person.party_id ,person.first_name, person.last_name from invoice INNER JOIN person on invoice.party_id = person.party_id where invoice_type_id=\'SALES_INVOICE\' AND status_id=\'INVOICE_READY\'');

        if($request->id_empleado != "")
            $arrData = DB::connection(getConnection(0))->select('select invoice_id, invoice_date, invoice_number, (sub_total_imp1+sub_total_imp2 + total_iva) as total , (CASE WHEN (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) IS NOT NULL THEN
            (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) ELSE 0 END)  as pagado, (sub_total_imp1+sub_total_imp2 + total_iva) - (CASE WHEN (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) IS NOT NULL THEN  
            (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) ELSE 0 END) as saldo, person.party_id ,person.first_name, person.last_name from invoice INNER JOIN person on invoice.party_id = person.party_id where invoice_type_id=\'SALES_INVOICE\' AND status_id=\'INVOICE_READY\' AND invoice.party_id=\''.$request->id_empleado.'\'');


        if($request->estado != "")
            $arrData = DB::connection(getConnection(0))->select('select invoice_id, invoice_date, invoice_number, (sub_total_imp1+sub_total_imp2 + total_iva) as total , (CASE WHEN (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) IS NOT NULL THEN
            (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) ELSE 0 END)  as pagado, (sub_total_imp1+sub_total_imp2 + total_iva) - (CASE WHEN (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) IS NOT NULL THEN  
            (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) ELSE 0 END) as saldo, person.party_id ,person.first_name, person.last_name from invoice INNER JOIN person on invoice.party_id = person.party_id where invoice_type_id=\'SALES_INVOICE\' AND status_id=\''.$request->estado.'\'');


        return view('layouts.views.consumos.admin.list',
            [
                'dataConsumo' => manualPagination($arrData,10),
                'dataEmpleados'   => ForeginContrataciones::join('person as p','contrataciones.party_id','p.party_id')
                    ->where('contrataciones.estado',1)->select('first_name','last_name','p.party_id')->distinct()->get(),
                'estado' => $request->estado
            ]);

    }

    public function formAdminConsumos(Request $request){
        return view('layouts.views.consumos.partials.form_consumo',[
            'consumo' => Consumos::where([
                ['invoice_id',$request->invoice_id],
                ['estado',0]
            ])->first(),
            'monto_pagado' => DB::connection(getConnection(0))->select('select invoice_id, invoice_date, invoice_number, (sub_total_imp1+sub_total_imp2 + total_iva) as total , (CASE WHEN (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) IS NOT NULL THEN
            (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) ELSE 0 END)  as pagado, (sub_total_imp1+sub_total_imp2 + total_iva) - (CASE WHEN (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) IS NOT NULL THEN  
            (select sum(amount_applied) from payment_application pa where pa.invoice_id = invoice.invoice_id) ELSE 0 END) as saldo, person.party_id ,person.first_name, person.last_name from invoice INNER JOIN person on invoice.party_id = person.party_id where invoice_type_id=\'SALES_INVOICE\' AND status_id=\'INVOICE_READY\' AND invoice_id=\''.$request->invoice_id.'\'')
        ]);
    }

    public function storeAdminConsumo(Request $request){
        $valida = Validator::make($request->all(), [
            'fecha_descuento' => 'required',
            'invoice_id' => 'required',
            'a_pagar' => 'required',
        ]);

        $msg = '';
        if (!$valida->fails()) {
            //dd($request->id_empleado);
            empty($request->id_consumo) ? $objConsumo = new Consumos : $objConsumo = Consumos::find($request->id_consumo);

            $objConsumo->id_empleado     = $request->id_empleado;
            $objConsumo->invoice_id      = $request->invoice_id;
            $objConsumo->fecha_descuento = Carbon::parse($request->fecha_descuento)->format('Y-m-05');
            $objConsumo->monto_descuento = $request->a_pagar;

            if ($objConsumo->save()) {
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                          Se ha creado el consumo con exito!
                       </div>';
            } else {
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                          Hubo un inconveniente al guardar los datos, intente nuevamente
                       </div>';
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
        return response()->json(['msg'=>$msg]);
    }

}
