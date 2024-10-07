<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\NominaController;
use App\Models\Contrataciones;
use App\Models\FechaFeriado;
use App\Models\HorasExtra;
use App\Models\ImagenesRoles;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Nomina;
use App\Models\NominasPasadas;
use App\Models\PartyIdentification;
use App\Models\PartyProfileDefault;
use App\Models\PaymentGlAccountTypeMap;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('actualiza_horas_extras',function(){

dd(App\Models\EftAccount::where([
        ['fi.fin_account_type_id','BANK_ACCOUNT'],
        ['status_id','FNACT_ACTIVE'],
        ['pm.thru_date',null]
    ])->join('payment_method as pm','eft_account.payment_method_id','pm.payment_method_id')
    ->join('fin_account as fi','pm.fin_account_id','fi.fin_account_id')
    ->join('eft_account as ea','pm.payment_method_id','ea.payment_method_id')
    ->select('ea.account_number','fi.fin_account_id','pm.party_id','fi.post_to_gl_account_id')->toSql());

    $nps= NominasPasadas::get();

    foreach($nps as $np){

        $party= PartyIdentification::where('id_value',$np->identificacion)->first();
        if(isset($party)){
            $inicio = Carbon::parse($np->fecha_nomina)->format('Y-m-01');
            $fin = Carbon::parse($np->fecha_nomina)->format('Y-m-'.getUltimoDiaMes($np->fecha_nomina));

            $hr50= getHorasExtras($party->party_id,0,$inicio,$fin,true,50,3);
            $hr100 = getHorasExtras($party->party_id,0,$inicio,$fin,true,100,3);

            $upNp = NominasPasadas::find($np->id_nominas_pasadas);
            $upNp->he_50 = $hr50;
            $upNp->he_100 = $hr100;
            $upNp->save();
        }
    }

    dd('Listo');

});

Route::get('horas_extras_pagadas/{idEmpleado}/{inicio}/{fin}',function($idEmpleado,$inicio,$fin){
    dump('50%');
    dump(getHorasExtras($idEmpleado,0,$inicio,$fin,true,50,3));
    dump('100%');
    dump(getHorasExtras($idEmpleado,0,$inicio,$fin,true,100,3));
});

Route::get('factura',function(){

    /*DB::beginTransaction();

    $dataLiquidacion = [
        'nombreEmpleado'          => 'Ana Gabriela-Espinosa Paladines',
        'documento'               => 'CÉDULA',
        'identificacion'          => '1716135189',
        'cargo'                   => 'VENDEDOR (A)',
        'idContrato'              => 91,
        'montoDecimoTercerSueldo' => 'N/A',
        'montoDecimoCuartoSueldo' => 'N/A',
        'montoVacaciones'         => 'N/A',
        'montoDesahucio'          => 'N/A',
        'montoDespidoIntempestivo'=> 'N/A',
        'montoHorasExtras'        => 0,
        'montoComisiones'         => 0,
        'arrPrestamos'            =>[],
        'arr_bonos_fijos'         => [],
        'montoConsumos'           => 0,
        'montoAnticipos'          => 300,
        'montoSalario'            => 550.00,
        'montoDescuentos'         => 0,
        'diasTrabajadosMesActual' => 0,
        'iva'                     => 0,
        'retencionRenta'          => 0,
        'retencionIva'            => 0,
        'montoTotalIngresos'      => 687.12,
        'montoTotalEgresos'       => 500,
        'montoTotalARecibir'      => 187.12,
        'bono25'                  => 0,
        'vistoBueno'              => 'N/A',
        'despidoIneficaz'         => 'N/A',
        'indemnizacionDiscapacidad' => 'N/A',
        'terminacionAntesPlazo' => 'N/A',
        'aportePersonal' => 'N/A'
    ];

    $fecha_terminacion= '2021-08-31';
    $idEmpleado= 23987;
    $nombre_archivo = $fecha_terminacion."_liquidacion_".$dataLiquidacion['identificacion']."_".$dataLiquidacion['nombreEmpleado'].".pdf";

    try{

        $dataContratacion = Contrataciones::where([
            ['contrataciones.id_contrataciones',91],
           //['contrataciones.estado',1],
            ['contrataciones.id_tipo_contrato_descripcion',2]
        ])->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')
        ->join('tipo_contrato as tc','contrataciones.id_tipo_contrato','tc.id_tipo_contrato')
        ->join('cargos as c','dc.id_cargo','c.id_cargo')
        ->select(
            'dc.retencion_iva',
            'dc.retencion_renta',
            'relacion_dependencia',
            'id_empleado',
            'dc.tipo_documento',
            'vacaciones',
            'fecha_expedicion_contrato',
            'contrataciones.id_contrataciones',
            'salario',
            'tc.horas_extras',
            'c.nombre',
            'dc.iva'
        )->first();

        $arrAnticipos[]=(object)[
            'id_anticipo' => 13,
            'cantidad' => 300
        ];
        $arrAnticipos[]=(object)[
            'id_anticipo' => 12,
            'cantidad' => 200
        ];

        $classNomina = new NominaController;
        $view = \View::make('layouts.views.nomina.partials.rol_pago_liquidacion', compact('dataLiquidacion'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->save(public_path('roles_pago') . '/'.$nombre_archivo);

        $objImagenRoles = new ImagenesRoles;
        $objImagenRoles->fecha_nomina  = Carbon::parse($fecha_terminacion)->format("Y-m-05");
        $objImagenRoles->nombre_imagen = $nombre_archivo;
        $objImagenRoles->id_empleado   = $idEmpleado;
        $objImagenRoles->tipo          = 2;
        $objImagenRoles->save();

        $objNomina = new Nomina;
        $objNomina->id_empleado  = $idEmpleado;
        $objNomina->fecha_nomina = Carbon::parse($fecha_terminacion)->format('Y-m-05');
        $objNomina->total        = number_format($dataLiquidacion['montoTotalARecibir'],2,".","");
        $objNomina->id_contrataciones = 91;
        $objNomina->persona = 'Ana Gabriela Espinosa Paladines';
        $objNomina->identificacion = '1716135189';
        $objNomina->liquidacion = true;
        $objNomina->save();
        $model = Nomina::orderBy('id_nomina','desc')->first();
        $invoice = $classNomina->generaFacturaHonorarios([
            'id_nomina' => $model->id_nomina,
            'honorarios' => 687.12,
            'contrataciones' => $dataContratacion,
            'date' => Carbon::parse($fecha_terminacion),
            'iva' => 0,
            'prestamos' => [],
            'anticipos' => $arrAnticipos,
            'otrosDescuentos' => [],
            'descripcion_invoice_item' => 'PAGO DE INGRESOS DE NÓMINA'
        ]);
        if(!$invoice['success']) throw new \Exception($invoice['msg']);

        $invoice['invoiceId'] = implode(',',$invoice['invoiceId']);
        $objNomina2 = Nomina::find($model->id_nomina);
        $objNomina2->update(['id_factura' => $invoice['invoiceId']]);

        DB::commit();
        dd($invoice);

    }catch(\Exception $e){

        DB::rollback();
        unlink(public_path('roles_pago') . '/'.$nombre_archivo);
        return $e->getMessage().' '.$e->getLine(). ' '.$e->getFile();

    }*/

});

Route::get('pago',function(){

   /*  $conexion = getConnection(0);

        DB::connection($conexion)->beginTransaction();
        DB::beginTransaction();

        try{

            $empresa = cuentaEmpresa();

            $liquidacion = Nomina::find(2540);

            $person = getPerson($liquidacion->id_empleado);

            $relacionDependencia = getRelacionDependencia($liquidacion->id_empleado)->relacion_dependencia;

            if(getRelacionDependencia($liquidacion->id_empleado)->relacion_dependencia){
                $paymentTypeId = 'PAYROL_PAYMENT';
            }else{
                $paymentTypeId = 'VENDOR_PAYMENT';
            }

            $liquidacion->total= 120.00;

            // CREA EL  PAGO
            $res = crearPago([
                'payment_type_id' => $paymentTypeId,
                'empresa' => $empresa,
                'referencia' => '0025282837',
                'comentario' => 'Pago '.($relacionDependencia ? 'liquidación' : 'ingreso de nómina').' '.$person->first_name.' '.$person->last_name,
                'tipo' => 'liquidacion',
                'id_registro' => $liquidacion->id_nomina,
                'monto' => $liquidacion->total,
                'person' => $person,
                'aplicado' =>true,
                'fecha' => now()->toDateString()
            ]);

            if(!$res['success']){
                $msgPersonal=true;
                throw new \Exception('No se pudo generar el pago '. $res['msg']);
            }

            // CREA EL ASIENTO DEL PAGO
            $paymentId = $res['paymentId'];

            $dataAcctg =[
                'acctg_trans_type_id' => 'OUTGOING_PAYMENT',
                'gl_fiscal_type_id' => 'ACTUAL',
                'is_posted' => 'Y',
                'party_id' => $liquidacion->id_empleado,
                'payment_id' => $paymentId,
                'description' =>  'Pago honorarios profesionales '.$person->first_name.' '.$person->last_name,
                'created_by_user_login' => session('dataUsuario')['id_usuario_log'],
                'last_modified_by_user_login' => session('dataUsuario')['id_usuario_log'],
                'role_type_id' => 'BILL_FROM_VENDOR'
            ];


            $glAccountDebito = glAccountMapPayment($paymentTypeId);
            $dataAcctg['debitos'][]= [
                'organization_party_id' => $empresa->party_id,
                'gl_account_id' => $glAccountDebito->gl_account_id,
                'amount' => $liquidacion->total
            ];

            // BANCOS
            $dataAcctg['creditos'][] =[
                'organization_party_id' => $empresa->party_id,
                'gl_account_id' => $empresa->post_to_gl_account_id,
                'amount' => $liquidacion->total
            ];

            $res = crearAcctgTrans($dataAcctg);
            if(!$res['success']){
                $msgPersonal=true;
                throw new \Exception('No se pudo crear el asiento contable '. $res['msg']);
            }

            //APLICA EL PAGO A LA FACTURA

            $facturasId = explode(',',$liquidacion->id_factura);

            $montoPago = $liquidacion->total;
            $montoP = $liquidacion->total;

            foreach($facturasId  as $facturaId){

                $invoice = Invoice::where('invoice_id',$facturaId)
                ->select(
                    DB::raw("
                        (select sum(invit.quantity*invit.amount) from invoice_item as invit where invit.invoice_id = invoice.invoice_id) -
                        (  case when
                                (select sum(pa.amount_applied) from payment_application as pa where pa.invoice_id =  invoice.invoice_id) is null
                            then 0
                            else
                                (select sum(pa.amount_applied) from payment_application as pa where pa.invoice_id =  invoice.invoice_id) end
                        ) as saldo
                    ")
                )->first();

                if($invoice->saldo <= $montoP){

                    $montoPago = $invoice->saldo;

                }

                if($montoP > 0){

                    $resPayment = paymentApplication([
                        'paymentId' => $paymentId,
                        'facturaId' => $facturaId,
                        'amount_applied' => $montoPago
                    ]);

                    if(!$resPayment['success'])
                        throw new Exception('No se pudo aplicar el pago de la factura '.$liquidacion->id_factura.' '.$resPayment['msg']);

                }

                $montoP -= $invoice->saldo;
            }

            DB::commit();
            DB::connection($conexion)->commit();
            $msg = '<div class="alert alert-success" role="alert" style="margin: 10px">
                        Se han generado los pagos correspondiente con éxito
                    </div>';
            $status = true;

        }catch(\Exception $e){

            DB::rollBack();
            DB::connection($conexion)->rollBack();

            $msg = '<div class="alert alert-danger" role="alert" style="margin: 10px">
                        Ha ocurrido un error al guardar generar los pagos <br /> '
                        .$e->getMessage().' '.(!isset($msgPersonal) ? $e->getFile().' '. $e->getLine() : '').'
                    </div>';
            $status = false;

        }

        return response()->json(['status'=>$status,'msg'=>$msg]);
 */
});

Route::post('hash','HashController@hash');

Route::get('login', function () { return view('layouts.login'); })->name('login');

Route::post('access_user', 'AccessController@accessUser')->name('access_user');

Route::group(['middleware' => ['CheckSession']], function () {

    Route::get('/','AccessController@index');
    Route::get('logout', 'AccessController@closeSession')->name('logout');

    ///////// MIDDLEWARE ADMINISTRADOR ///////////
    Route::group(['middleware' => ['CheckRolAdministrador']], function () {

        Route::resource('tipo-contrato', 'TipoContratoController');
        Route::resource('configuracion-empresa', 'ConfiguracionEmpresaController');
        Route::resource('contrato', 'ContratoController');
        Route::resource('cargos', 'CargoController');
        Route::resource('anulacion-contrato', 'MotivoAnulacionController');
        Route::resource('documentos', 'DocumentosController');
        Route::resource('productos', 'ProductosController');
        Route::resource('tipo-comisiones', 'TipoComisionController');
        Route::resource('iva', 'IvaController');
        Route::resource('proyeccion-nomina', 'ProyeccionNominaController');
        include 'complementarias/admin.php';

    });

    ///////// MIDDLEWARE EMPLEADO ///////////
    Route::group(['middleware' => ['CheckRolEmpleado']], function () {

        Route::resource('horas-extras', 'HoraExtraController');
        Route::resource('vacaciones', 'VacacionesController');
        Route::resource('consumos', 'ConsumosController');
        Route::resource('anticipos', 'AnticiposController');
        Route::get('ficha','UsuarioController@fichaUsuario')->name('ficha');
        Route::get('roles-pago-empleado','NominaController@reporteRolesPagoEmpleado')->name('vista.roles-pago-empleado');
        include 'complementarias/empleado.php';

    });


    ///////// MIDDLEWARE PARA DAR CIERTOS PERMISOS DE ADMIN A ALGUNOS ROLES ///////////
    Route::group(['middleware' => ['Permiso']], function () {

        Route::resource('contrataciones', 'ContratacionesController');
        Route::resource('empleados', 'EmpleadoController');
        Route::resource('horarios', 'HorariosEmpleadosController');
        Route::resource('otros-descuentos','OtrosDescuentosController');
        Route::resource('comisiones', 'ComisionesController');
        Route::resource('nomina', 'NominaController');
        Route::get('comsiones/add_inputs', 'ComisionesController@addInputs')->name('vista.add_inputs_comisiones');
        include 'complementarias/permisos/contrataciones/rutas.php';
        include 'complementarias/permisos/empleados/rutas.php';
        include 'complementarias/permisos/horarios/rutas.php';
        include 'complementarias/permisos/horas_extras/rutas.php';
        include 'complementarias/permisos/otros_descuentos/rutas.php';
        include 'complementarias/permisos/admin_anticipos/rutas.php';
        include 'complementarias/permisos/admin_vacaciones/rutas.php';
        include 'complementarias/permisos/admin_consumos/rutas.php';
        include 'complementarias/permisos/nomina/rutas.php';

    });
});

