<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anticipos;
use App\Models\Person;
use App\Models\ForeginContrataciones;
use App\Mail\MailAnticipos;
use App\Models\FinAccountTrans;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Party;
use App\Models\PartyAcctgPreference;
use App\Models\Payment;
use App\Models\PaymentApplication;
use App\Models\ProductStore;
use App\Models\ReferenciaPago;
use App\Models\SequenceValueItem;
use Illuminate\Support\Facades\Mail;
use Validator;
use DB;
use Exception;

class AdminAnticiposController extends Controller
{
    public function adminAnticipos(Request $request){

        $data= Anticipos::where(function($q) use ($request){
            if(!empty($request->fecha) && !empty($request->fecha))
                $q->where('fecha_solicitud',$request->fecha);

            if(!empty($request->estado))
                $q->where('estado',$request->estado);

            if(!empty($request->id_empleado))
                $q->where('id_empleado',$request->id_empleado);

        })->orderBy('id_anticipo','Desc')
            ->where('estado',empty($request->estado) ? 0 : $request->estado)->get();

        $arrData = [];

        for($i=0;$i<$data->count();$i++){

            $b = Person::where('party_id',$data[$i]->id_empleado)->first();
            $arrData[] = [
                'id_anticipo'     => $data[$i]->id_anticipo,
                'id_empleado'     => $data[$i]->id_empleado,
                'fecha_entrega'   => $data[$i]->fecha_entrega,
                'fecha_descuento' => $data[$i]->fecha_descuento,
                'estado'          => $data[$i]->estado,
                'cantidad'        => $data[$i]->cantidad,
                'nombre'          => $b->first_name." ".$b->last_name
            ];
        }

        return view('layouts.views.anticipos.admin.list',
            [
                'dataAnticipo' => manualPagination($arrData,10),
                'dataEmpleados' => ForeginContrataciones::join('person as p','contrataciones.party_id','p.party_id')
                    ->where('contrataciones.estado',1)->select('first_name','last_name','p.party_id')->distinct()->get(),
                'cantAntcipos' => Anticipos::whereNotIn('id_anticipo',function($query){
                    $query->select('id_registro')->from('referencia_pago')->where('tipo','anticipo');
                })->where('estado',1)->count() && $request->estado == '1',

            ]);

    }

    public function formComentarioAnticipoNoAprobado(Request $request){
        return view('layouts.views.anticipos.admin.form_anticipo_no_aprobado',[
            'dataAnticipo' => Anticipos::where('id_anticipo',$request->id_anticipo)->select('id_anticipo','comentario')->first()
        ]);
    }

    public function storeComentarioAnticipoNoAprobado(Request $request){

        $valida =  Validator::make($request->all(), [
            'comentario' => 'required',
        ]);

        if(!$valida->fails()) {

            $objAnticipo = Anticipos::find($request->id_anticipo);
            $objAnticipo->comentario = $request->comentario;
            $objAnticipo->estado = 2;

            if($objAnticipo->save()){

                $msg = '<div class="alert alert-success" role="alert" style="margin-bottom: 10px">
                            El comentario ha sido enviado y no se ha aprobado este anticipo, de igual forma se ha enviado un correo electrónico al empleado para notificarlo
                        </div>';

                $dataAnticipo = Anticipos::where('anticipos.id_anticipo',$request->id_anticipo)->first();
                $nombreEmpleado = Person::where('party_id', $dataAnticipo->id_empleado)->select('first_name','last_name')->first();
                $status = 1;
                $message1 = $request->comentario;
                $estado=0;

                Mail::to(getMailEmpleado($dataAnticipo->id_empleado))->send(new MailAnticipos($estado,$message1,getMailEmpleado($dataAnticipo->id_empleado),$dataAnticipo,$nombreEmpleado));

            }else{

                $msg = '<div class="alert alert-danger" role="alert" style="margin-bottom: 10px">
                              Ocurrió un error al intentar enviar el comentario, intente nuevamente
                           </div>';
                $status = 0;
            }

        }else {
            $msg='';
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

    public function editAnticipoAdmin(Request $request)
    {

        return view('layouts.views.anticipos.admin.form_edit_anticipo',
            [
                'dataAnticipo' => Anticipos::where('id_anticipo',$request->id_anticipo)->first()
            ]);
    }

    public function storeAnticipoAdmin(Request $request)
    {
        $valida =  Validator::make($request->all(), [
            //'cantidad'        => 'required',
            'fecha_entrega'   => 'required',
            'fecha_descuento' => 'required',
        ]);

        $msg='';
        if(!$valida->fails()) {

            $objAnticipo = Anticipos::find($request->id_anticipo);
            //$objAnticipo->cantidad        = $request->cantidad;
            $objAnticipo->fecha_entrega   = $request->fecha_entrega;
            $objAnticipo->fecha_descuento = $request->fecha_descuento;

            if ($objAnticipo->save()) {
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                            Anticipo editado con éxito
                        </div>';
                $status = 1;

            } else{
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                            Hubo un inconveniente al editar el anticipo, intente nuevamente
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

    public function aprobarAnticipo(Request $request)
    {
        $msg = '';

        try{

            foreach ($request->arrIdAnticiposAprobados as $key => $idAnticipo){

                $dataAnticipo = Anticipos::find($idAnticipo);

                if(getRelacionDependencia($dataAnticipo->id_empleado)->relacion_dependencia){

                    try{

                        $conexion = getConnection(0);

                        DB::connection($conexion)->beginTransaction();

                        //CREAR INVOICE Y EL ASIENTO DEL INVOICE
                        $person = getPerson($dataAnticipo->id_empleado);

                        $partyEmpresa= Party::join('party_role as pr','pr.role_type_id','pr.role_type_id')
                                ->join('role_type as rt','pr.role_type_id','pr.role_type_id')
                                ->where([
                                    ['pr.role_type_id' , 'INTERNAL_ORGANIZATIO'],
                                    ['rt.role_type_id' , 'INTERNAL_ORGANIZATIO']
                                ])->first();

                        $nItemInvoice= 0;
                        $seqInvoice= PartyAcctgPreference::where('party_id',$partyEmpresa->party_id)->first();
                        $seqInvoiceId = $seqInvoice->last_invoice_number+1;
                        $store = ProductStore::where('type_store','MATRIZ')->first();

                        $invoice= new Invoice;
                        $invoice->invoice_id = 'FA'.$seqInvoiceId;
                        $invoice->invoice_number = $dataAnticipo->id_anticipo;
                        $invoice->invoice_type_id = 'PAYROL_INVOICE';
                        $invoice->party_id_from = $dataAnticipo->id_empleado;
                        $invoice->party_id = $partyEmpresa->party_id;
                        $invoice->product_store_id= $store->product_store_id;
                        $invoice->status_id = 'INVOICE_READY';
                        $invoice->currency_uom_id='USD';
                        $invoice->due_date = now()->toDateTimeString();
                        $invoice->invoice_date = now()->toDateTimeString();
                        $invoice->description ='Anticipo aprobado al empleado '.$person->first_name.' '.$person->last_name;
                        $invoice->last_updated_stamp = now()->toDayDateTimeString();
                        $invoice->last_updated_tx_stamp = now()->toDayDateTimeString();
                        $invoice->created_stamp = now()->toDayDateTimeString();
                        $invoice->created_tx_stamp = now()->toDayDateTimeString();
                        $invoice->save();

                        $nItemInvoice++;
                        $inoviceItem = new InvoiceItem;
                        $inoviceItem->invoice_id = $invoice->invoice_id;
                        $inoviceItem->invoice_item_seq_id = str_pad(($nItemInvoice),5,'0',STR_PAD_LEFT);
                        $inoviceItem->invoice_item_type_id = 'REG_ANTIC_ROL_PAGO';
                        $inoviceItem->quantity= 1.000000;
                        $inoviceItem->amount= $dataAnticipo->cantidad;
                        $inoviceItem->description = 'Anticipo de rol';
                        $inoviceItem->last_updated_stamp = now()->toDayDateTimeString();
                        $inoviceItem->last_updated_tx_stamp = now()->toDayDateTimeString();
                        $inoviceItem->created_stamp = now()->toDayDateTimeString();
                        $inoviceItem->created_tx_stamp = now()->toDayDateTimeString();
                        $inoviceItem->save();

                        PartyAcctgPreference::where('party_id',$partyEmpresa->party_id)->update(['last_invoice_number' =>$seqInvoiceId]);

                        $empresa = cuentaEmpresa();

                        $glAccountDebito = glAccountMapInvoice($inoviceItem->invoice_item_type_id);

                        if(!isset($glAccountDebito)){
                            $msgPersonal=true;
                            throw new Exception('No existe una cuenta contable configurada para el tipo de pago VENDOR_PREPAY');
                        }

                        $glAccountCredito = glAccountMapPayment('PAYROL_PAYMENT');

                        if(!isset($glAccountCredito)){
                            $msgPersonal=true;
                            throw new Exception('No existe una cuenta contable configurada para el tipo de pago PAYROL_PAYMENT');
                        }

                        $dataAcctg =[
                            'acctg_trans_type_id' => 'PAYROL_INVOICE',
                            'gl_fiscal_type_id' => 'ACTUAL',
                            'is_posted' => 'Y',
                            'party_id' => $dataAnticipo->id_empleado,
                            'invoice_id' => $invoice->invoice_id,
                            'description' => 'Anticipo de pago de sueldo a '. $person->first_name .' '.$person->last_name,
                            'created_by_user_login' => session('dataUsuario')['id_usuario_log'],
                            'last_modified_by_user_login' => session('dataUsuario')['id_usuario_log'],
                            'role_type_id' => 'EMPLOYEE'
                        ];

                        //ANTICIPOS DE ROL DE PAGOS
                        $dataAcctg['debitos'][]= [
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => $glAccountDebito->gl_account_id,
                            'amount' => $dataAnticipo->cantidad
                        ];

                        //SUELDOS POR PAGAR
                        $dataAcctg['creditos'][] =[
                            'organization_party_id' => $empresa->party_id,
                            'gl_account_id' => $glAccountCredito->gl_account_id,
                            'amount' => $dataAnticipo->cantidad
                        ];

                        $res = crearAcctgTrans($dataAcctg);

                        if(!$res['success']){
                            $msgPersonal=true;
                            throw new \Exception('No se pudo crear el asiento contable del anticipo '. $res['msg']);
                        }

                        DB::connection($conexion)->commit();

                        $dataAnticipo->invoice_id = $invoice->invoice_id;

                    }catch(\Exception $e){

                        DB::connection($conexion)->rollback();

                        return response()->json([
                            'status'=>false,
                            'msg'=> '<div class="alert alert-danger" role="alert" style="margin-bottom: 10px">
                                        No se pudo aprobar el anticipo '
                                        .$e->getMessage().' '.(!isset($msgPersonal) ? $e->getLine().' '.$e->getFile() : '').'
                                    </div>'
                        ]);
                    }

                }

                $dataAnticipo->estado = 1;
                $dataAnticipo->save();

                $dataAnticipo = Anticipos::where('id_anticipo',$idAnticipo)->first();

                $message1 = $request->comentario;
                $estado=1;

                Mail::to(getMailEmpleado($dataAnticipo->id_empleado))
                        ->send(new MailAnticipos($estado,$message1,getMailEmpleado($dataAnticipo->id_empleado),$dataAnticipo));

            }

            $msg .= '<div class="alert alert-success" role="alert" style="margin-bottom: 10px">
                        Se han aprobado los anticipos seleccionados, y se ha enviado un correo electrónico al empleado para notificarlo
                    </div>';
            $status = 1;

        }catch(\Exception $e){

            $msg .= '<div class="alert alert-danger" role="alert" style="margin-bottom: 10px">
                        No se pudo aprobar el anticipo '
                        .$e->getMessage().' '.(!isset($msgPersonal) ? $e->getLine().' '.$e->getFile() : '').'
                    </div>';
            $status = 0;

        }

        return response()->json(['status'=>$status,'msg'=>$msg]);
    }

    public function formCashManagementAnticipo()
    {
        return view('layouts.views.anticipos.admin.form_cash_management');
    }

    public function downloadCashManagementAnticipo(Request $request)
    {
        $cuentaEmpresa = cuentaEmpresa();

        $anticiposAprobados = Anticipos::where([
            ['estado', '1'],
            ['descontado',false]
        ])->get();

        $dataFile='';

        foreach($anticiposAprobados as $anticipo){

            $dataContratacion = contratacionesCashManagement($anticipo->id_empleado)->first();

            $dataFile.= "PA\t".str_pad($cuentaEmpresa->account_number,20)."\t1\t1\t".str_pad($dataContratacion->id_value,13)."\tUSD\t".str_pad(str_replace(['.',','],'',number_format($anticipo->cantidad,2)),13,'0',STR_PAD_LEFT)."\tCTA\t". str_pad($dataContratacion->enum_code2,13)."\t".$dataContratacion->tipo_cuenta."\t".str_pad($dataContratacion->account_number,20)."\t".$dataContratacion->codigo_identificacion."\t".str_pad($dataContratacion->id_value,14)."\t".str_pad("PAGO ANTICIPO ". $dataContratacion->empleado,40)."\t\n";

        }

        return base64_encode($dataFile);
    }

    public function storeReferenciaBancariaAnticipo(Request $request)
    {
        $valida =  Validator::make($request->all(), [
            'referencia'   => 'required',
        ],[
            'referencia.required' => 'La referencia es obligatoria'
        ]);

        $msg = '';
        $status = false;

        if(!$valida->fails()) {

            $conexion = getConnection(0);

            DB::connection($conexion)->beginTransaction();
            DB::beginTransaction();

            try{

                $anticipos = Anticipos::whereNotIn('id_anticipo',function($query){
                    $query->select('id_registro')->from('referencia_pago')->where('tipo','anticipos');
                })->where('estado',1)->get();

                $empresa = cuentaEmpresa();

                foreach($anticipos as $anticipo){

                    $relacionDependencia = getRelacionDependencia($anticipo->id_empleado)->relacion_dependencia;

                    $existePago = ReferenciaPago::where([
                        'referencia' => $request->referencia,
                        'id_registro' => $anticipo->id_anticipo,
                        'tipo' => 'anticipos'
                    ])->exists();

                    $person = getPerson($anticipo->id_empleado);

                    if($existePago){
                        $msgPersonal=true;
                        throw new Exception('<br />Ya existe el pago para el anticipo del empleado '.$person->first_name .' '.$person->last_name.' por el monto '.$anticipo->cantidad);
                    }

                    $res = crearPago([
                        'payment_type_id' => $relacionDependencia ? 'ROL_PREPAY' : 'VENDOR_PREPAY',
                        'empresa' => $empresa,
                        'referencia' => $request->referencia,
                        'comentario' => 'Anticipo de '. $relacionDependencia ? 'sueldo' : 'honorarios',
                        'tipo' => 'anticipos',
                        'id_registro' => $anticipo->id_anticipo,
                        'monto' => $anticipo->cantidad,
                        'person' => $person,
                        'fecha' => now()->toDateString()
                    ]);

                    if(!$res['success']){
                        $msgPersonal=true;
                        throw new \Exception('No se pudo generara el pago '. $res['msg']);
                    }

                    $paymentId = $res['paymentId'];

                    if($relacionDependencia){

                        $glAccount='PAYROL_PAYMENT';
                        $glAccountDebito = glAccountMapPayment($glAccount); // SUELDOS POR PAGAR

                        $resPayment = paymentApplication([
                            'paymentId' => $paymentId,
                            'facturaId' => $anticipo->invoice_id,
                            'amount_applied' => $anticipo->cantidad
                        ]);

                        if(!$resPayment['success'])
                            throw new Exception('No se pudo aplicar el pago de la factura '.$anticipo->invoice_id.' '.$resPayment['msg']);

                        ReferenciaPago::where([
                            'referencia' => $request->referencia,
                            'id_registro' => $anticipo->id_anticipo,
                            'tipo' => 'anticipos',
                            'payment_id' => $paymentId,
                            'fecha'=> now()->toDateString(),
                            'aplicado' => true
                        ])->update(['aplicado'=> true]);

                    }else{

                        $glAccount='DESCTO_PAGO';
                        $glAccountDebito = glAccountMapPayment($glAccount); //CUENTAS POR PAGAR

                    }

                    if(!isset($glAccountDebito)){
                        $msgPersonal=true;
                        throw new Exception('No existe una cuenta contable configurada para el tipo de pago '.$glAccount);
                    }

                    $dataAcctg =[
                        'acctg_trans_type_id' => 'OUTGOING_PAYMENT',
                        'gl_fiscal_type_id' => 'ACTUAL',
                        'is_posted' => 'Y',
                        'party_id' => $anticipo->id_empleado,
                        'payment_id' => $paymentId,
                        'description' => 'Anticipo de pago a sueldo a '. $person->first_name .' '.$person->last_name,
                        'created_by_user_login' => session('dataUsuario')['id_usuario_log'],
                        'last_modified_by_user_login' => session('dataUsuario')['id_usuario_log'],
                        'role_type_id' => 'BILL_FROM_VENDOR'
                    ];

                    $dataAcctg['debitos'][]= [
                        'organization_party_id' => $empresa->party_id,
                        'gl_account_id' => $glAccountDebito->gl_account_id,
                        'amount' => $anticipo->cantidad
                    ];

                    //BANCOS
                    $dataAcctg['creditos'][] =[
                        'organization_party_id' => $empresa->party_id,
                        'gl_account_id' => $empresa->post_to_gl_account_id,
                        'amount' => $anticipo->cantidad
                    ];

                    $res = crearAcctgTrans($dataAcctg);

                    if(!$res['success']){
                        $msgPersonal=true;
                        throw new \Exception('No se pudo crear el asiento contable '. $res['msg']);
                    }

                    $anticipo = Anticipos::find($anticipo->id_anticipo);
                    $anticipo->update(['estado'=> 4]);
                }

                DB::commit();
                DB::connection($conexion)->commit();
                $status = true;
                $msg .= '<div class="alert alert-success" role="alert" style="margin-bottom: 10px">
                              Se ha generado el pago de los anticipos aprobados
                          </div>';

            }catch(\Exception $e){

                $status = false;
                $msg .= '<div class="alert alert-danger" role="alert" style="margin-bottom: 10px">
                                No pudo ser generado el pago de los anticipos '
                                .$e->getMessage().' '.(!isset($msgPersonal) ? $e->getLine().' '.$e->getFile() : '').'
                            </div>';
                DB::rollback();
                DB::connection($conexion)->rollback();

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

}
