<?php

namespace App\Http\Controllers;

use App\Models\Anticipos;
use App\Models\Cargo;
use App\Models\Consumos;
use App\Models\ImagenesRoles;
use App\Models\Nomina;
use App\Models\OtrosDescuentos;
use App\Models\Vacaciones;
use Illuminate\Http\Request;
use App\Models\TipoContratos;
use App\Models\Contrato;
use App\Models\Comisiones;
use App\Models\ConfiguracionEmpresa;
use App\Models\TipoIdentificacionGrupo;
use App\Models\SequenceValueItem;
use App\Models\Person;
use App\Models\Party;
use App\Models\Geo;
use App\Models\BonoFijo;
use App\Models\Addendum;
use App\Models\PartyIdentification;
use App\Models\PartyRole;
use App\Models\ContactMetch;
use App\Models\PartyContactMech;
use App\Models\PostalAddres;
use App\Models\TelecomNumber;
use App\Models\PartyRelationShip;
use App\Models\Contrataciones;
use App\Models\DetalleContratacion;
use App\Models\ForeginContrataciones;
use App\Models\MotivoAnulacion;
use App\Models\FinalizacionContratacion;
use App\Models\ImagenesDetallesContrataciones;
use App\Models\ConfiguracionVariablesEmpresa;
use App\Models\DecimoTercero;
use App\Models\DeductionType;
use App\Models\EftAccount;
use App\Models\Enumeration;
use App\Models\FinAccountTrans;
use App\Models\InvoiceType;
use App\Models\Iva;
use App\Models\NominasPasadas;
use App\Models\OauthClient;
use App\Models\PartyProfileDefault;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Storage;
use Validator;
use App\Models\Prestamo;
use App\Models\ProductStore;
use App\Models\ReferenciaPago;
use App\Models\UserLogin;
use App\Models\UserLoginTnt;
use App\Models\VacacionesNomina;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use PDF;

class ContratacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $dataContrataciones = ForeginContrataciones::where([
            ['estado',isset($request->estado) ? $request->estado : 1],
            ['id_tipo_contrato_descripcion',isset($request->tipo_contrato) ? $request->tipo_contrato : 2 ]
        ])->join('person as p','contrataciones.party_id','p.party_id')
            ->select('p.first_name','p.last_name','p.party_id')->distinct()->get();


//	dd( $dataContrataciones);
        $arrDataContrataciones = [];

        foreach($dataContrataciones as $key => $clavesContratos){

            $d = Contrataciones::where([
                ['id_empleado',$clavesContratos->party_id],
                ['estado',isset($request->estado) ? $request->estado : 1],
                ['id_tipo_contrato_descripcion',isset($request->tipo_contrato) ? $request->tipo_contrato : 2 ]
            ])->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','=','dc.id_contrataciones');

            if($request->estado == 3)
                $d = $d->join('finalizacion_contrataciones as fc','dc.id_contrataciones','fc.id_contrataciones');

            $d = $d->get();

            foreach ($d as $item) {
                $tc = Contrato::where('contrato.id_tipo_contrato',$item->id_tipo_contrato)
                    ->join('tipo_contrato as tc', 'contrato.id_tipo_contrato','=', 'tc.id_tipo_contrato')
                    ->select('tc.nombre','tc.nombre','contrato.id_contrato','tc.descripcion','tc.id_tipo_contrato_descripcion')->first();

                $fecha_finalizacion = false;
                if(isset($item->fecha_finalizacion))
                    $fecha_finalizacion = $item->fecha_finalizacion;

                if(!empty($request->tipo_contrato)){

                    if($item->id_tipo_contrato_descripcion == $request->tipo_contrato)
                        $arrDataContrataciones[] =
                            [
                                'idContrataciones'   => $item->id_contrataciones,
                                'tipoContratacion'   => $tc->id_tipo_contrato_descripcion,
                                'idEmpleado'         => $item->id_empleado,
                                'idContrato'         => $item->id_contrato,
                                'estado'             => $item->estado,
                                'idCargo'            => $item->id_cargo,
                                'contrato'           => $item->nombre_archivo_contrato,
                                'expedicionContrato' => $item->fecha_expedicion_contrato,
                                'tipoContrato'       => $tc->nombre,
                                'descripcionContrato'=> $tc->descripcion,
                                'expiracionContrato' => $item->fecha_expiracion_contrato,
                                'nombre'             => $dataContrataciones[$key]->first_name ." ". $dataContrataciones[$key]->last_name,
                                'fecha_finalizacion' => $fecha_finalizacion
                            ];
                }else{

                    if($item->id_tipo_contrato_descripcion == 2 || ($item->id_tipo_contrato_descripcion == 1))
                        $arrDataContrataciones[] =
                            [
                                'idContrataciones'   => $item->id_contrataciones,
                                'tipoContratacion'   => $tc->id_tipo_contrato_descripcion,
                                'idEmpleado'         => $item->id_empleado,
                                'idContrato'         => $item->id_contrato,
                                'estado'             => $item->estado,
                                'idCargo'            => $item->id_cargo,
                                'contrato'           => $item->nombre_archivo_contrato,
                                'expedicionContrato' => $item->fecha_expedicion_contrato,
                                'tipoContrato'       => $tc->nombre,
                                'descripcionContrato'=> $tc->descripcion,
                                'expiracionContrato' => $item->fecha_expiracion_contrato,
                                'nombre'             => $dataContrataciones[$key]->first_name ." ". $dataContrataciones[$key]->last_name,
                                'fecha_finalizacion' => $fecha_finalizacion
                            ];

                }
            }

        }

        return view('layouts.views.contrataciones.list',[
            'arrDataContrataciones' => manualPagination($arrDataContrataciones, 10),
            'cantPrestamos' => Prestamo::join('contrataciones as c','prestamo.id_contratacion','c.id_contrataciones')
            ->whereNotIn('id_prestamo',function($query){
                $query->select('id_registro')->from('referencia_pago')->where('tipo','prestamo');
            })->where('c.estado',1)->count()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $dataContratacion = null;
        $dataCargos = null;
        $dataProvinicias = null;
        $dataEmpleados = null;
        $dataTipoIdentificacionGrupo = null;

        if(isset($request->idContratacion)){

            $dataContratacion = Contrataciones::where('contrataciones.id_contrataciones',$request->idContratacion)
            ->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')
                ->join('tipo_contrato as tc','contrataciones.id_tipo_contrato','tc.id_tipo_contrato')
                ->join('contrato as c','contrataciones.id_tipo_contrato','c.id_tipo_contrato')->first();

            $dataTipoIdentificacionGrupo = TipoIdentificacionGrupo::select('party_identification_type_id','description')->get();
            $dataCargos = Cargo::all();
            $dataProvinicias = Geo::where([
                ['geo_type_id','PROVINCE'],
                ['geo_id', 'like', 'EC%']
            ])->get();

            $dataEmpleados = PartyRole::join('person as p', 'party_role.party_id','=','p.party_id')
                ->join('party_identification as pi','p.party_id','=','pi.party_id')
                ->join('party_contact_mech as pcm', 'p.party_id','=','pcm.party_id')
                ->join('contact_mech as cm','pcm.contact_mech_id','=','cm.contact_mech_id')
                ->leftJoin('payment_method as pm',function($q){
                    $q->on('p.party_id','pm.party_id')->whereNull("pm.thru_date");
                })->join('eft_account as ea','pm.payment_method_id','ea.payment_method_id');

            $existPartyRelationship = PartyRelationShip::where('party_id_from',$dataContratacion->id_empleado)->first();

            if($existPartyRelationship != null){
                $dataEmpleados->join('party_relationship as prs','party_role.party_id','prs.party_id_from')
                ->join('person as per', 'prs.party_id_to','=','per.party_id')
                ->join('party_contact_mech as pcmc', 'per.party_id','=','pcmc.party_id');

                $dataEmpleados = $dataEmpleados->select(
                    'p.party_id',
                    'p.first_name',
                    'p.first_name',
                    'p.last_name',
                    'p.nacionalidad',
                    'party_role.role_type_id',
                    'p.gender',
                    'p.birth_date',
                    'pi.party_identification_type_id',
                    'pi.id_value',
                    'cm.contact_mech_id',
                    'cm.info_string',
                    'ea.account_type',
                    'ea.account_number',
                    'ea.codigo_banco',
                    ($existPartyRelationship != null ? 'per.party_id as party_id_contact' : 'cm.info_string'),
                    ($existPartyRelationship != null ? 'per.first_name as first_name_contact' : 'cm.info_string'),
                    ($existPartyRelationship != null ? 'per.last_name as last_name_contact' : 'cm.info_string'),

                );

            }else{

                $dataEmpleados = $dataEmpleados->select(
                    'p.party_id',
                    'p.first_name',
                    'p.first_name',
                    'p.last_name',
                    'p.nacionalidad',
                    'party_role.role_type_id',
                    'p.gender',
                    'p.birth_date',
                    'pi.party_identification_type_id',
                    'pi.id_value',
                    'cm.contact_mech_id',
                    'cm.info_string',
                    'ea.account_type',
                    'ea.account_number',
                    'ea.codigo_banco'
                );

            }

            $dataEmpleados = $dataEmpleados->where([
            	['party_role.party_id',$dataContratacion->id_empleado],
            	['party_role.role_type_id','EMPLOYEE']
            ])->first();

        }

        $sueldo_sectorial = 0;
        //if(isset($dataContratacion->sueldo_sectorial) && $dataContratacion->sueldo_sectorial)
        //$sueldo_sectorial = Cargo::where('id_cargo',$dataContratacion->id_cargo)->select('sueldo_minimo_sectorial')->first()->sueldo_minimo_sectorial;

        return view('layouts.views.contrataciones.partials.form_contrataciones',
            [
                'dataTipoContratos' => TipoContratos::where('estado',1)
                    ->whereIn('id_tipo_contrato', function ($query){
                        $query->select('id_tipo_contrato')->from('contrato')->where('estado',true);
                    })->get(),
                'dataContratacion' => $dataContratacion,
                'dataCargos' => $dataCargos,
                'dataProvinicias' => $dataProvinicias,
                'dataEmpleados' => $dataEmpleados,
                'dataTipoIdentificacionGrupo' => $dataTipoIdentificacionGrupo,
                'sueldo_sectorial' => $sueldo_sectorial,
                'bancos' => Enumeration::where('enum_type_id','TIPO_BANCO')->orderBy('description', 'asc')->get(),
                'iva' => Iva::all(),
                'tipoDocumentos'=> tipoDocumentosNomina(),
                'tipoImpuestos' => DeductionType::where('activo','S')->get()
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
        if($request->has('tipo_empleado')){

            $valida =  Validator::make($request->all(), [
                'salario'            => 'required',
                'fecha_inicio'       => 'required',
                'horas'              => 'required',
                'id_cargo'           => 'required',
                'id_tipo_contrato'   => 'required',
                'nombres'            => 'required',
                'apellidos'          => 'required',
                'nacimiento'         => 'required',
                'genero'             => 'required',
                'tipo_identificacion'=> 'required',
                'identificacion'     => 'required',
                'correo'             => 'required|email',
                'telefono'           => 'required',
                'C_V'                => 'required',
                'nacionalidad'       => 'required'
             ]);
        }
        else{
            $valida =  Validator::make($request->all(), [
                'id_empleado'      => 'required',
                'id_tipo_contrato' => 'required',
            ]);
        }

        if(!$valida->fails()) {

            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                     hubo un error al realizar la contratación, intente nuevamente
               </div>';
            $status = 0;

            $objContrato      = Contrato::where('id_tipo_contrato',$request->id_tipo_contrato)->first();
            $objConfigEmpresa = ConfiguracionEmpresa::all();

            $partyIdentification = PartyIdentification::where('id_value',$request->identificacion)
               ->rightJoin('party_role as pr','party_identification.party_id','pr.party_id');


            if($request->has('tipo_empleado')){ //USUARIO NUEVOS DATOS

                if($partyIdentification->count() > 0){

                    if($partyIdentification->where('role_type_id','EMPLOYEE')->count() > 0){ //SI YA ES EMPLEADO
                        $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                                    Este número de identificación ya se encuentra registrado en el sistema como empleado, destilde la opción "Usuario nuevo" y proceda a buscar al empleado en al lista del campo Empleado!
                                </div>';
                        $status = 0;

                    }else{ //SI NO ES EMPLEADO SE LE ASIGNA EL ROL EMPLEADO

                        $objPartyRole = new PartyRole;
                        $objPartyRole->party_id = PartyIdentification::where('id_value',$request->identificacion)->first()->party_id;
                        $objPartyRole->role_type_id = 'EMPLOYEE';
                        $objPartyRole->status = true;
                        $objPartyRole->save();
                        /* $objPerson = Person::find($objPartyRole->party_id);
                        $objPerson->update(['nacionalidad' => $request->nacionalidad]); */
                        $request['id_empleado'] = $objPartyRole->party_id;
                        unset($request['tipo_empleado']);
                        /*$status = 1;
                        $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                                    Los datos de esta persona ya existien en el sistema, le ha sido agregado el rol de empleado, destilde la opción "Usuario nuevo" y proceda a buscar al empleado en al lista del campo Empleado
                               </div>'; */

                        //dd($request->has('tipo_empleado'));
                    }

                }else{

                    $seqNextParty = SequenceValueItem::where('seq_name','Party')->select('seq_id')->first()->seq_id + 1;

                    $objSequenceValueItem = SequenceValueItem::find('Party');
                    $objSequenceValueItem->seq_id = $seqNextParty;

                    if($objSequenceValueItem->save()){

                       $objParty = new Party;
                       $objParty->party_id      = $seqNextParty;
                       $objParty->party_type_id = 'PERSON';
		       $objParty->status_id = 'PARTY_ENABLED';

                       if($objParty->save()){

                           $partyContrato = $seqNextParty;
                           $objPerson = new Person;
                           $objPerson->party_id      = $seqNextParty;
                           $objPerson->first_name    = $request->nombres;
                           $objPerson->last_name     = $request->apellidos;
                           $objPerson->gender        = $request->genero;
                           $objPerson->birth_date    = $request->nacimiento;
                           $objPerson->nacionalidad  = $request->nacionalidad;

                           if($objPerson->save()){

                               $objPartyIdentificacion = new PartyIdentification;
                               $objPartyIdentificacion->party_id                      = $seqNextParty;
                               $objPartyIdentificacion->party_identification_type_id  = $request->tipo_identificacion;
                               $objPartyIdentificacion->id_value                      = $request->identificacion;

                                if($objPartyIdentificacion->save()){

                                    $userLoginId = $request->identificacion;

                                    if(OauthClient::where('client_id',$userLoginId)->exists() || UserLogin::where('user_login_id',$userLoginId)->exists())
                                        $userLoginId = "123456";

                                    $oauthClient = new OauthClient;
                                    $oauthClient->client_id = $userLoginId;
                                    $oauthClient->client_secret = Hash::make($userLoginId);
                                    $oauthClient->redirect_uri = '/oauth/receivecode';
                                    $oauthClient->save();

                                    $userLogin = new UserLogin;
                                    $userLogin->user_login_id = $userLoginId;
                                    $userLogin->party_id = $seqNextParty;
                                    $userLogin->current_password = '{SHA}'.sha1($userLoginId);
                                    $userLogin->enabled ='Y';
                                    $userLogin->last_updated_stamp = now()->toDateTimeString();
                                    $userLogin->last_updated_tx_stamp = now()->toDateTimeString();
                                    $userLogin->created_stamp = now()->toDateTimeString();
                                    $userLogin->created_tx_stamp = now()->toDateTimeString();
                                    $userLogin->save();

                                    $userLoginTnt = new UserLoginTnt;
                                    $userLoginTnt->user_login_id = $userLoginId;
                                    $userLoginTnt->party_id = $seqNextParty;
                                    $userLoginTnt->current_password = '{SHA}'.sha1($userLoginId);
                                    $userLoginTnt->enabled ='Y';
                                    $userLoginTnt->last_updated_stamp = now()->toDateTimeString();
                                    $userLoginTnt->last_updated_tx_stamp = now()->toDateTimeString();
                                    $userLoginTnt->created_stamp = now()->toDateTimeString();
                                    $userLoginTnt->created_tx_stamp = now()->toDateTimeString();
                                    $userLoginTnt->save();

                                    $objPartyRole = new PartyRole;
                                    $objPartyRole->party_id = $seqNextParty;
                                    $objPartyRole->role_type_id = 'EMPLOYEE';
                                    $objPartyRole->status = true;

                                   if($objPartyRole->save()){

                                       $seqNextContactMech = SequenceValueItem::where('seq_name','ContactMech')
                                               ->select('seq_id')->first()->seq_id + 1;

                                       $objSequenceValueItem = SequenceValueItem::find('ContactMech');
                                       $objSequenceValueItem->seq_id = $seqNextContactMech;

                                       if($objSequenceValueItem->save()){
                                           $contacMechPersonEmailAddress = $seqNextContactMech;
                                           $objContactMetch = new ContactMetch;
                                           $objContactMetch->contact_mech_id      = $seqNextContactMech;
                                           $objContactMetch->contact_mech_type_id = 'EMAIL_ADDRESS';
                                           $objContactMetch->info_string          =  $request->correo;

                                           if($objContactMetch->save()){

                                               $objPartyContactMech = new PartyContactMech;
                                               $objPartyContactMech->party_id        = $seqNextParty;
                                               $objPartyContactMech->contact_mech_id = $seqNextContactMech;
                                               $objPartyContactMech->role_type_id    = 'EMPLOYEE';
                                               $objPartyContactMech->from_date       = date('Y/m/d');

                                                if(!$objPartyContactMech->save()){
                                                    ContactMetch::destroy($contacMechPersonEmailAddress);
                                                    PartyRole::destroy($partyContrato);
                                                    PartyIdentification::destroy($partyContrato);
                                                    Person::destroy($partyContrato);
                                                    Party::destroy($partyContrato);
                                                    $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                                                        ha ocurrido un erro al crear la contratación, intenta nuevamente
                                                    </div>';
                                                    return response()->json(array('status'=>false,'msg'=>$msg));
                                                }

                                                $seqNextContactMech = SequenceValueItem::where('seq_name','ContactMech')
                                                       ->select('seq_id')->first()->seq_id + 1;

                                                $objSequenceValueItem = SequenceValueItem::find('ContactMech');
                                                $objSequenceValueItem->seq_id = $seqNextContactMech;

                                                if($objSequenceValueItem->save()) {

                                                    $contacMechPersonTelecomNumber = $seqNextContactMech;
                                                    $objContactMetch = new ContactMetch;
                                                    $objContactMetch->contact_mech_id      = $seqNextContactMech;
                                                    $objContactMetch->contact_mech_type_id = 'TELECOM_NUMBER';

                                                    if(!$objContactMetch->save()){
                                                        PartyContactMech::destroy($partyContrato);
                                                        ContactMetch::destroy($contacMechPersonEmailAddress);
                                                        PartyRole::destroy($partyContrato);
                                                        PartyIdentification::destroy($partyContrato);
                                                        Person::destroy($partyContrato);
                                                        Party::destroy($partyContrato);
                                                        $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                                                                    ha ocurrido un erro al crear la contratación, intenta nuevamente
                                                               </div>';
                                                       return response()->json(array('status'=>false,'msg'=>$msg));
                                                    }

                                                    $objTelecomNumber = new TelecomNumber;
                                                    $objTelecomNumber->contact_mech_id = $seqNextContactMech;
                                                    $objTelecomNumber->country_code = '593';
                                                    $objTelecomNumber->contact_number = $request->telefono;


                                                    if($objTelecomNumber->save()){

                                                       $objPartyContactMech = new PartyContactMech;
                                                       $objPartyContactMech->party_id        = $seqNextParty;
                                                       $objPartyContactMech->contact_mech_id = $seqNextContactMech;
                                                       $objPartyContactMech->role_type_id    = 'EMPLOYEE';
                                                       $objPartyContactMech->from_date       = date('Y/m/d');

                                                        if($objPartyContactMech->save()){

                                                            $seqNextContactMech = SequenceValueItem::where('seq_name','ContactMech')
                                                                   ->select('seq_id')->first()->seq_id + 1;

                                                            $objSequenceValueItem = SequenceValueItem::find('ContactMech');
                                                            $objSequenceValueItem->seq_id = $seqNextContactMech;

                                                            if($objSequenceValueItem->save()){
                                                               $contacMechPersonPostalAddress = $seqNextContactMech;

                                                               $objContactMetch = new ContactMetch;
                                                               $objContactMetch->contact_mech_id      = $seqNextContactMech;
                                                               $objContactMetch->contact_mech_type_id = 'POSTAL_ADDRESS';

                                                               if(!$objContactMetch->save()){
                                                                   PartyContactMech::destroy($partyContrato);
                                                                   ContactMetch::destroy($contacMechPersonTelecomNumber);
                                                                   TelecomNumber::destroy($contacMechPersonTelecomNumber);
                                                                   PartyRole::destroy($partyContrato);
                                                                   PartyIdentification::destroy($partyContrato);
                                                                   Person::destroy($partyContrato);
                                                                   Party::destroy($partyContrato);
                                                                   $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                                                                                ha ocurrido un error al crear la contratación, intenta nuevamente
                                                                           </div>';
                                                                   return response()->json(array('status'=>false,'msg'=>$msg));
                                                               }

                                                               $objPostalAddres = new PostalAddres;
                                                               $objPostalAddres->address1              = $request->C_V;
                                                               $objPostalAddres->city                  = $request->ciudad;
                                                               $objPostalAddres->country_geo_id        = 'ECU';
                                                               $objPostalAddres->state_province_geo_id = $request->id_provincia;
                                                               $objPostalAddres->contact_mech_id       = $seqNextContactMech;

                                                               if($objPostalAddres->save()){

                                                                   $objPartyContactMech = new PartyContactMech;
                                                                   $objPartyContactMech->party_id        = $seqNextParty;
                                                                   $objPartyContactMech->contact_mech_id = $seqNextContactMech;
                                                                   $objPartyContactMech->role_type_id    = 'EMPLOYEE';
                                                                   $objPartyContactMech->from_date       = date('Y/m/d');
                                                                    if(!$objPartyContactMech->save()){
                                                                        PartyContactMech::destroy($partyContrato);
                                                                        ContactMetch::destroy($contacMechPersonTelecomNumber);
                                                                        PostalAddres::destroy($contacMechPersonPostalAddress);
                                                                        ContactMetch::destroy($contacMechPersonPostalAddress);
                                                                        PartyRole::destroy($partyContrato);
                                                                        PartyIdentification::destroy($partyContrato);
                                                                        Person::destroy($partyContrato);
                                                                        Party::destroy($partyContrato);
                                                                        $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                                                                                ha ocurrido un error al crear la contratación, intenta nuevamente
                                                                           </div>';
                                                                        return response()->json(array('status'=>false,'msg'=>$msg));
                                                                    }

                                                                    if((!empty($request->nombre_contacto) &&  $request->nombre_contacto != null) &&
                                                                        (!empty($request->apellido_contacto) && $request->apellido_contacto != null) &&
                                                                        (!empty($request->telefono_contacto) && $request->telefono_contacto != null)){

                                                                            //////////////////  CONTACTO ////////////////
                                                                        $seqNextPartyContacto = SequenceValueItem::where('seq_name','Party')
                                                                               ->select('seq_id')->first()->seq_id + 1;

                                                                        $objSequenceValueItem = SequenceValueItem::find('Party');
                                                                        $objSequenceValueItem->seq_id = $seqNextPartyContacto;

                                                                        if($objSequenceValueItem->save()){

                                                                            $objPartyContacto = new Party;
                                                                            $objPartyContacto->party_id      = $seqNextPartyContacto;
                                                                            $objPartyContacto->party_type_id = 'PERSON';

                                                                            if($objPartyContacto->save()){
                                                                                $objPersonContacto = new Person;
                                                                                $objPersonContacto->party_id   = $seqNextPartyContacto;
                                                                                $objPersonContacto->first_name = $request->nombre_contacto;
                                                                                $objPersonContacto->last_name  = $request->apellido_contacto;

                                                                                if($objPersonContacto->save()){

                                                                                   $seqNextContactMech = SequenceValueItem::where('seq_name','ContactMech')
                                                                                           ->select('seq_id')->first()->seq_id + 1;

                                                                                   $objSequenceValueItemContacto = SequenceValueItem::find('ContactMech');
                                                                                   $objSequenceValueItemContacto->seq_id = $seqNextContactMech;

                                                                                   if($objSequenceValueItemContacto->save()){

                                                                                       $objContactMetchContacto = new ContactMetch;
                                                                                       $objContactMetchContacto->contact_mech_id     = $seqNextContactMech;
                                                                                       $objContactMetchContacto->contact_mech_type_id='TELECOM_NUMBER';

                                                                                       if($objContactMetchContacto->save()){

                                                                                           $objTelecomNumbercontacto = new TelecomNumber;
                                                                                           $objTelecomNumbercontacto->contact_mech_id = $seqNextContactMech;
                                                                                           $objTelecomNumbercontacto->country_code    = '593';
                                                                                           $objTelecomNumbercontacto->contact_number  = $request->telefono_contacto;

                                                                                           if($objTelecomNumbercontacto->save()){

                                                                                               $objPartyRoleContacto = new PartyRole;
                                                                                               $objPartyRoleContacto->party_id    = $seqNextPartyContacto;
                                                                                               $objPartyRoleContacto->role_type_id= 'CONTACTO_EMERGENCIA';

                                                                                               if($objPartyRoleContacto->save()){

                                                                                                   $objPartyContactMechContacto = new PartyContactMech;
                                                                                                   $objPartyContactMechContacto->party_id        = $seqNextPartyContacto;
                                                                                                   $objPartyContactMechContacto->contact_mech_id = $seqNextContactMech;
                                                                                                   $objPartyContactMechContacto->role_type_id    = 'CONTACTO_EMERGENCIA';
                                                                                                   $objPartyContactMechContacto->from_date       = date('Y/m/d');

                                                                                                   if($objPartyContactMechContacto->save()){

                                                                                                       $objPartyRelationShipContacto = new PartyRelationShip;
                                                                                                       $objPartyRelationShipContacto->party_id_from     = $seqNextParty;
                                                                                                       $objPartyRelationShipContacto->party_id_to       = $seqNextPartyContacto;
                                                                                                       $objPartyRelationShipContacto->role_type_id_from = 'EMPLOYEE';
                                                                                                       $objPartyRelationShipContacto->role_type_id_to   = 'CONTACTO_EMERGENCIA';
                                                                                                       $objPartyRelationShipContacto->party_relationship_type_id ='CONTACTO_EMER';
                                                                                                       $objPartyRelationShipContacto->from_date         = date('Y/m/d');
                                                                                                       if($objPartyRelationShipContacto->save()){
                                                                                                           $f = explode("-",$request->fecha_inicio);
                                                                                                           if($request->has('activa')){
                                                                                                               $datos = [
                                                                                                                   ucwords($objConfigEmpresa[0]->nombre_empresa),
                                                                                                                   $objConfigEmpresa[0]->ruc,
                                                                                                                   $objConfigEmpresa[0]->direccion_empresa,
                                                                                                                   ucwords($objConfigEmpresa[0]->representante),
                                                                                                                   $objConfigEmpresa[0]->identificacion_representante,
                                                                                                                   $request->ciudad . " " . $request->C_V,
                                                                                                                   isset($request->id_cargo) ? Cargo::where('id_cargo',$request->id_cargo)->select('nombre')->first()->nombre : '',
                                                                                                                   isset($request->salario) ? $request->salario : '',
                                                                                                                   isset($request->horas) ? $request->horas : '',
                                                                                                                   $request->nacionalidad == null
                                                                                                                       ? $dataPerson = Person::where('person.party_id',$request->id_empleado)->select('person.nacionalidad')->first()->nacionalidad
                                                                                                                       : $request->nacionalidad,
                                                                                                                   isset($f[2]) ? $f[2] : null,
                                                                                                                   isset($f[1]) ? $f[1] : null,
                                                                                                                   isset($f[0]) ? $f[0] : null,
                                                                                                                   isset($request->cant_dias) ? $request->cant_dias : null,
                                                                                                                   $request->correo,
                                                                                                                   $request->funciones,
                                                                                                                   $request->id_ciudad

                                                                                                               ];
                                                                                                           }
                                                                                                           $objContrataciones = new Contrataciones;
                                                                                                           $objContrataciones->id_empleado                  = $seqNextParty;
                                                                                                           $objContrataciones->id_tipo_contrato             = $request->id_tipo_contrato;
                                                                                                           $objContrataciones->id_tipo_contrato_descripcion = tipoContratoDescripcion($request->id_tipo_contrato)->id_tipo_contrato_descripcion;
                                                                                                           $request->has('activa') ? $objContrataciones->estado = 1 : 0;

                                                                                                           if($objContrataciones->save()){
                                                                                                               $model = Contrataciones::orderBy('id_contrataciones','desc')->first();
                                                                                                               $objDetalleContratacion = new DetalleContratacion;
                                                                                                               $objDetalleContratacion->id_contrataciones           = $model->id_contrataciones;
                                                                                                               $objDetalleContratacion->id_cargo                    = $request->id_cargo;
                                                                                                               $objDetalleContratacion->fecha_expedicion_contrato   = isset($request->fecha_inicio) ? $request->fecha_inicio : null;
                                                                                                               $objDetalleContratacion->horas_jornada_laboral       = isset($request->horas) ? $request->horas : null;
                                                                                                               $objDetalleContratacion->nombre_archivo_contrato     = $request->has('activa') ? makeContrato($request->nombres,$request->apellidos, $request->identificacion,$datos,$objContrato->cuerpo_contrato) : null;
                                                                                                               $objDetalleContratacion->salario                     = isset($request->salario) ? $request->salario : '';
                                                                                                               $objDetalleContratacion->tipo_documento              = isset($request->tipo_documento) ? $request->tipo_documento : '';
                                                                                                               $objDetalleContratacion->decimo_tercero              = isset($request->decimo_tercero) ? $request->decimo_tercero : null;
                                                                                                               $objDetalleContratacion->decimo_cuarto               = isset($request->decimo_cuarto) ? $request->decimo_cuarto : null;
                                                                                                               $objDetalleContratacion->fondo_reserva               = isset($request->fondo_reserva) ? $request->fondo_reserva : null;
                                                                                                               $objDetalleContratacion->cantidad_letras             = isset($request->letras) ? $request->letras : null;
                                                                                                               $objDetalleContratacion->duracion                    = isset($request->cant_dias) ? $request->cant_dias : null;
                                                                                                               $objDetalleContratacion->retencion_iva               = isset($request->retencion_iva) ? $request->retencion_iva : null;
                                                                                                               $objDetalleContratacion->retencion_renta             = isset($request->retencion_renta) ? $request->retencion_renta : null;
                                                                                                               $objDetalleContratacion->id_ciudad                   = isset($request->id_ciudad) ? $request->id_ciudad : null;
                                                                                                               $objDetalleContratacion->funciones                   = isset($request->funciones) ? $request->funciones : null;
                                                                                                               $objDetalleContratacion->iva                         = isset($request->iva) ? $request->iva : null;
                                                                                                               $objDetalleContratacion->tipo_retencion_renta        = isset($request->tipo_impuesto_renta) ? $request->tipo_impuesto_renta : null;
                                                                                                               $objDetalleContratacion->tipo_retencion_iva          = isset($request->tipo_impuesto_iva) ? $request->tipo_impuesto_iva : null;

                                                                                                               if($objDetalleContratacion->save()){
                                                                                                                    $objForeginContrataciones = new ForeginContrataciones;
                                                                                                                    $objForeginContrataciones->id_contrataciones            = $model->id_contrataciones;
                                                                                                                    $objForeginContrataciones->party_id                     = $seqNextParty;
                                                                                                                    $objForeginContrataciones->id_tipo_contrato             = $request->id_tipo_contrato;
                                                                                                                    $objForeginContrataciones->id_tipo_contrato_descripcion = tipoContratoDescripcion($request->id_tipo_contrato)->id_tipo_contrato_descripcion;
                                                                                                                    $request->has('activa') ? $objForeginContrataciones->estado = 1 : 0;
                                                                                                                    if($objForeginContrataciones->save()){

                                                                                                                        $seqNextPaymentMethod = SequenceValueItem::where('seq_name','PaymentMethod')
                                                                                                                            ->select('seq_id')->first()->seq_id;

                                                                                                                        $objPaymentMethod = new PaymentMethod;
                                                                                                                        $objPaymentMethod->payment_method_id = $seqNextPaymentMethod+1;
                                                                                                                        $objPaymentMethod->payment_method_type_id = 'EFT_ACCOUNT'; //TRANSFERENCIA BANCARIA
                                                                                                                        $objPaymentMethod->party_id= $seqNextParty;
                                                                                                                        $objPaymentMethod->description= $request->nombres.' '.$request->apellidos;
                                                                                                                        $objPaymentMethod->from_date=now()->toDateTimeString();
                                                                                                                        $objPaymentMethod->created_stamp=now()->toDateTimeString();
                                                                                                                        $objPaymentMethod->created_tx_stamp=now()->toDateTimeString();
                                                                                                                        if($objPaymentMethod->save()){

                                                                                                                            $objeftAcount = new EftAccount;
                                                                                                                            $objeftAcount->payment_method_id= $objPaymentMethod->payment_method_id;
                                                                                                                            $objeftAcount->account_type= $request->tipo_cuenta;
                                                                                                                            $objeftAcount->codigo_banco = $request->id_banco;
                                                                                                                            $objeftAcount->account_number = $request->numero_cuenta;
                                                                                                                            $objeftAcount->name_on_account = $request->nombres.' '.$request->apellidos;
                                                                                                                            $objeftAcount->created_tx_stamp = now()->toDateTimeString();
                                                                                                                            $objeftAcount->created_stamp = now()->toDateTimeString();

                                                                                                                            if($objeftAcount->save()){

                                                                                                                                if(isset($request->tipo_impuesto_renta) && isset($request->tipo_impuesto_iva) && $request->tipo_documento == 'INVOICE_HONORARIOS'){

                                                                                                                                    $retIva= explode('*',$request->tipo_impuesto_iva);
                                                                                                                                    $retRenta= explode('*',$request->tipo_impuesto_renta);
                                                                                                                                    $store = ProductStore::where('type_store','MATRIZ')->first();

                                                                                                                                    PartyProfileDefault::updateOrCreate(
                                                                                                                                        ['party_id' => $objParty->party_id],
                                                                                                                                        [
                                                                                                                                            'product_store_id' => $store->product_store_id ,
                                                                                                                                            'party_id' => $objParty->party_id,
                                                                                                                                            'default_pay_meth' => 'EFT_ACCOUNT',
                                                                                                                                            'ret_ir_id' => $retRenta[1],
                                                                                                                                            'ret_iva_id' => $retIva[1],
                                                                                                                                            'last_updated_stamp' => now()->format('Y-m-d H:i:s'),
                                                                                                                                            'last_updated_tx_stamp' => now()->format('Y-m-d H:i:s'),
                                                                                                                                            'created_stamp' => now()->format('Y-m-d H:i:s'),
                                                                                                                                            'created_tx_stamp' => now()->format('Y-m-d H:i:s')
                                                                                                                                        ]
                                                                                                                                    );

                                                                                                                                }else{
                                                                                                                                    PartyProfileDefault::where('party_id',$objParty->party_id)->delete();
                                                                                                                                }

                                                                                                                                $objSequenceValueItem= SequenceValueItem::where('seq_name','PaymentMethod');
                                                                                                                                $objSequenceValueItem->update(['seq_id' => $objPaymentMethod->payment_method_id]);

                                                                                                                                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                                                                                                                                            El contrato se ha guardado con éxito!
                                                                                                                                        </div>';
                                                                                                                                        $status = 1;
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                       ///////////// FIN CONTACTO ///////////

                                                                   }

                                                                   /*if($objPartyContactMech->save()){


                                                                   }*/
                                                               }else{
                                                                   PartyContactMech::destroy($partyContrato);
                                                                   ContactMetch::destroy($contacMechPersonEmailAddress);
                                                                   ContactMetch::destroy($contacMechPersonTelecomNumber);
                                                                   PartyRole::destroy($partyContrato);
                                                                   PartyIdentification::destroy($partyContrato);
                                                                   Person::destroy($partyContrato);
                                                                   Party::destroy($partyContrato);
                                                                   $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                                                                                ha ocurrido un erro al crear la contratación, intenta nuevamente
                                                                           </div>';
                                                                   return response()->json(array('status'=>false,'msg'=>$msg));
                                                               }
                                                           }
                                                       }
                                                   }else{
                                                       ContactMetch::destroy($contacMechPersonTelecomNumber);
                                                       PartyRole::destroy($partyContrato);
                                                       PartyIdentification::destroy($partyContrato);
                                                       Person::destroy($partyContrato);
                                                       Party::destroy($partyContrato);
                                                       $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                                                                    ha ocurrido un erro al crear la contratación, intenta nuevamente
                                                               </div>';
                                                       return response()->json(array('status'=>false,'msg'=>$msg));
                                                   }
                                               }
                                           }else{
                                               PartyRole::destroy($partyContrato);
                                               PartyIdentification::destroy($partyContrato);
                                               Person::destroy($partyContrato);
                                               Party::destroy($partyContrato);
                                               $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                                                            ha ocurrido un erro al crear la contratación, intenta nuevamente
                                                       </div>';
                                               return response()->json(array('status'=>false,'msg'=>$msg));
                                           }
                                       }
                                   }else{
                                       PartyIdentification::destroy($partyContrato);
                                       Person::destroy($partyContrato);
                                       Party::destroy($partyContrato);
                                       $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                                            ha ocurrido un erro al crear la contratación, intenta nuevamente
                                       </div>';
                                       return response()->json(array('status'=>false,'msg'=>$msg));
                                   }
                               }else{
                                   Person::destroy($partyContrato);
                                   Party::destroy($partyContrato);
                                   $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                                            ha ocurrido un erro al crear la contratación, intenta nuevamente
                                       </div>';
                                   return response()->json(array('status'=>false,'msg'=>$msg));
                               }
                           }else{
                               Party::destroy($partyContrato);
                               $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                                            ha ocurrido un erro al crear la contratación, intenta nuevamente
                                       </div>';
                               return response()->json(array('status'=>false,'msg'=>$msg));
                           }
                       }
                    }
                }
            }


            if(!$request->has('tipo_empleado')){ // USUARIO DATOS REHUSADOS O EMPLEADO NUEVO PERO LOS DATOS YA EXISTEN EN LA BD

                if(getExistContrataciones($request->id_empleado)){
                    $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                                El empleado ya tiene ambos contratos activos actualmente (Contratación y Confidencialidad)
                            </div>';
                    $status = 0;

                }else{

                    if($request->has('C_V') || $request->has('ciudad')){
                        $seqNextContactMech = SequenceValueItem::where('seq_name','ContactMech')
                                ->select('seq_id')->first()->seq_id + 1;

                        $objSequenceValueItem = SequenceValueItem::find('ContactMech');
                        $objSequenceValueItem->seq_id = $seqNextContactMech;

                        if($objSequenceValueItem->save()){

                            $objContactMech = new ContactMetch;
                            $objContactMech->contact_mech_id      = $seqNextContactMech;
                            $objContactMech->contact_mech_type_id = 'POSTAL_ADDRESS';

                            if($objContactMech->save()){
                                $objPostalAddres = new PostalAddres;
                                $objPostalAddres->address1              = $request->C_V;
                                $objPostalAddres->city                  = $request->ciudad;
                                $objPostalAddres->country_geo_id        = 'ECU';
                                $objPostalAddres->state_province_geo_id = $request->id_provincia;
                                $objPostalAddres->contact_mech_id       = $seqNextContactMech;
                                if($objPostalAddres->save()){
                                    $objPartyContactMech = new PartyContactMech;
                                    $objPartyContactMech->party_id        = $request->id_empleado;
                                    $objPartyContactMech->contact_mech_id = $seqNextContactMech;
                                    $objPartyContactMech->role_type_id    = 'EMPLOYEE';
                                    $objPartyContactMech->from_date       = date('Y/m/d');
                                    $objPartyContactMech->save();
                                }
                            }
                        }

                    }

                    if($request->has('correo')){
                        $seqNextContactMech = SequenceValueItem::where('seq_name','ContactMech')
                                ->select('seq_id')->first()->seq_id + 1;

                        $objSequenceValueItem = SequenceValueItem::find('ContactMech');
                        $objSequenceValueItem->seq_id = $seqNextContactMech;

                        if($objSequenceValueItem->save()){
                            $objContactMetch = new ContactMetch;
                            $objContactMetch->contact_mech_id      = $seqNextContactMech;
                            $objContactMetch->contact_mech_type_id = 'EMAIL_ADDRESS';
                            $objContactMetch->info_string          =  $request->correo;
                            if($objContactMetch->save()){
                                $objPartyContactMech = new PartyContactMech;
                                $objPartyContactMech->party_id        = $request->id_empleado;
                                $objPartyContactMech->contact_mech_id = $seqNextContactMech;
                                $objPartyContactMech->role_type_id    = 'EMPLOYEE';
                                $objPartyContactMech->from_date       = date('Y/m/d');}
                                $objPartyContactMech->save();
                        }
                    }

                    if($request->has('telefono')){
                        $seqNextContactMech = SequenceValueItem::where('seq_name','ContactMech')
                                ->select('seq_id')->first()->seq_id + 1;

                        $objSequenceValueItem = SequenceValueItem::find('ContactMech');
                        $objSequenceValueItem->seq_id = $seqNextContactMech;

                        if($objSequenceValueItem->save()) {

                            $objContactMetch = new ContactMetch;
                            $objContactMetch->contact_mech_id = $seqNextContactMech;
                            $objContactMetch->contact_mech_type_id = 'TELECOM_NUMBER';

                            if ($objContactMetch->save()) {

                                $objTelecomNumber = new TelecomNumber;
                                $objTelecomNumber->contact_mech_id = $seqNextContactMech;
                                $objTelecomNumber->country_code = '593';
                                $objTelecomNumber->contact_number = $request->telefono;

                                if ($objTelecomNumber->save()) {

                                    $objPartyContactMech = new PartyContactMech;
                                    $objPartyContactMech->party_id = $request->id_empleado;
                                    $objPartyContactMech->contact_mech_id = $seqNextContactMech;
                                    $objPartyContactMech->role_type_id = 'EMPLOYEE';
                                    $objPartyContactMech->from_date = date('Y/m/d');
                                    $objPartyContactMech->save();
                                }
                            }
                        }
                    }

                    $dataPerson = Person::where([
                        ['person.party_id',$request->id_empleado],
                        ['pr.role_type_id','EMPLOYEE']
                    ])->join('party_role as pr','person.party_id','pr.party_id')
                        ->join('party_identification as pi', 'person.party_id','=','pi.party_id')
                        ->select('person.first_name','person.last_name','pi.id_value','person.nacionalidad')->first();

                    if(isset($dataPerson) && ($dataPerson->nacionalidad == "" || $dataPerson->nacionalidad == null)){
                        $objPerson = Person::where('party_id',$request->id_empleado);
                        $objPerson->update(['nacionalidad'=>$request->nacionalidad]);
                    }

                    $f = explode("-",$request->fecha_inicio);
                    $postalAdress = getPostalAddres($request->id_empleado);

                    if($request->has('activa')) {

                        $correo = PartyContactMech::join('contact_mech as cm','party_contact_mech.contact_mech_id','cm.contact_mech_id')
                                    ->where([
                                        ['party_contact_mech.party_id',$request->id_empleado],
                                        ['cm.contact_mech_type_id','EMAIL_ADDRESS']
                                    ])->select('cm.info_string')->first();

                        $datos = [
                            ucwords($objConfigEmpresa[0]->nombre_empresa),
                            $objConfigEmpresa[0]->ruc,
                            $objConfigEmpresa[0]->direccion_empresa,
                            ucwords($objConfigEmpresa[0]->representante),
                            $objConfigEmpresa[0]->identificacion_representante,
                            ucwords((isset($postalAdress->city) ? $postalAdress->city : $request->ciudad) . " " . (isset($postalAdress->address1) ? $postalAdress->address1 : $request->C_V)),
                            isset($request->id_cargo) ? Cargo::where('id_cargo',$request->id_cargo)->select('nombre')->first()->nombre : null,
                            isset($request->salario) ? $request->salario : null,
                            isset($request->horas) ? $request->horas : null,
                            isset($dataPerson) ? $dataPerson->nacionalidad: '',
                            isset($f[2]) ? $f[2] : null,
                            isset($f[1]) ? $f[1] : null,
                            isset($f[0]) ? $f[0] : null,
                            isset($request->cant_dias) ? $request->cant_dias : null,
                            isset($correo) ? $correo->info_string : null,
                            $request->funciones,
                            $request->id_ciudad
                        ];
                    }

                    $objContrataciones = new Contrataciones;
                    $objContrataciones->id_empleado                  = $request->id_empleado;
                    $objContrataciones->id_tipo_contrato             = $request->id_tipo_contrato;
                    $objContrataciones->id_tipo_contrato_descripcion = tipoContratoDescripcion($request->id_tipo_contrato)->id_tipo_contrato_descripcion;
                    $request->has('activa') ? $objContrataciones->estado = 1 : 0;

                    if($objContrataciones->save()) {

                        $model = Contrataciones::orderBy('id_contrataciones','desc')->first();
                        $objDetalleContratacion = new DetalleContratacion;
                        $objDetalleContratacion->id_contrataciones           = $model->id_contrataciones;
                        $objDetalleContratacion->id_cargo                    = isset($request->id_cargo) ? $request->id_cargo : null;
                        $objDetalleContratacion->fecha_expedicion_contrato   = isset($request->fecha_inicio) ? $request->fecha_inicio : null;//isset($fechaInicioFormateada) ? $fechaInicioFormateada : null;
                        $objDetalleContratacion->horas_jornada_laboral       = isset($request->horas) ? $request->horas : null; //isset($horasTrabajo) ? $horasTrabajo : null;
                        $objDetalleContratacion->nombre_archivo_contrato     = $request->has('activa') ? makeContrato($dataPerson->first_name,$dataPerson->last_name,$dataPerson->id_value,$datos,$objContrato->cuerpo_contrato) : null;
                        $objDetalleContratacion->salario                     = isset($request->salario) ? $request->salario : null;
                        $objDetalleContratacion->decimo_tercero              = isset($request->decimo_tercero) ? $request->decimo_tercero : null;
                        $objDetalleContratacion->tipo_documento              = isset($request->tipo_documento) ? $request->tipo_documento : '';
                        $objDetalleContratacion->decimo_cuarto               = isset($request->decimo_cuarto) ? $request->decimo_cuarto : null;
                        $objDetalleContratacion->fondo_reserva               = isset($request->fondo_reserva) ? $request->fondo_reserva : null;
                        $objDetalleContratacion->cantidad_letras             = isset($request->letras) ? $request->letras : null;
                        $objDetalleContratacion->duracion                    = isset($request->cant_dias) ? $request->cant_dias : null;
                        $objDetalleContratacion->retencion_iva               = isset($request->retencion_iva) ? $request->retencion_iva : null;
                        $objDetalleContratacion->retencion_renta             = isset($request->retencion_renta) ? $request->retencion_renta : null;
                        $objDetalleContratacion->id_ciudad                   = isset($request->id_ciudad) ? $request->id_ciudad : null;
                        $objDetalleContratacion->funciones                   = isset($request->funciones) ? $request->funciones : null;
                        $objDetalleContratacion->iva                         = isset($request->iva) ? $request->iva : null;

                        if($objDetalleContratacion->save()){

                            $objForeginContrataciones = new ForeginContrataciones;
                            $objForeginContrataciones->id_contrataciones            = $model->id_contrataciones;
                            $objForeginContrataciones->party_id                     = $request->id_empleado;
                            $objForeginContrataciones->id_tipo_contrato             = $request->id_tipo_contrato;
                            $objForeginContrataciones->id_tipo_contrato_descripcion = tipoContratoDescripcion($request->id_tipo_contrato)->id_tipo_contrato_descripcion;
                            $request->has('activa') ? $objForeginContrataciones->estado = 1 : 0;

                            if ($objForeginContrataciones->save()) {

                                $existPaymenttMethod= PaymentMethod::where([
                                    ['party_id',$request->id_empleado],
                                    ['thru_date',null]
                                ])->first();

                                $person = Person::where('party_id',$request->id_empleado)->first();

                                if(isset($existPaymenttMethod)){

                                    $objeftAcount= EftAccount::find($existPaymenttMethod->payment_method_id);
                                    $objeftAcount->update([
                                        'account_type' => $request->tipo_cuenta,
                                        'codigo_banco' => $request->id_banco,
                                        'account_number' => $request->numero_cuenta,
                                        'name_on_account' => $person->first_name.' '.$person->last_name,
                                        'last_updated_stamp' => now()->toDateTimeString(),
                                        'last_updated_tx_stamp' => now()->toDateTimeString()
                                    ]);

                                }else{

                                    $seqNextPaymentMethod = SequenceValueItem::where('seq_name','PaymentMethod')->select('seq_id')->first()->seq_id;

                                    $objPaymentMethod = new PaymentMethod;
                                    $objPaymentMethod->payment_method_id =  $seqNextPaymentMethod+1;
                                    $objPaymentMethod->payment_method_type_id = 'EFT_ACCOUNT'; //TRANSFERENCIA BANCARIA
                                    $objPaymentMethod->party_id= $request->id_empleado;
                                    $objPaymentMethod->description=$person->first_name.' '.$person->last_name;
                                    $objPaymentMethod->from_date=now()->toDateTimeString();
                                    $objPaymentMethod->created_stamp=now()->toDateTimeString();
                                    $objPaymentMethod->created_tx_stamp=now()->toDateTimeString();

                                    if($objPaymentMethod->save()){

                                        $objeftAcount = new EftAccount;
                                        $objeftAcount->payment_method_id= $objPaymentMethod->payment_method_id;
                                        $objeftAcount->account_type= $request->tipo_cuenta;
                                        $objeftAcount->codigo_banco = $request->id_banco;
                                        $objeftAcount->account_number = $request->numero_cuenta;
                                        $objeftAcount->name_on_account = $request->nombres.' '.$request->apellidos;
                                        $objeftAcount->created_tx_stamp = now()->toDateTimeString();
                                        $objeftAcount->created_stamp = now()->toDateTimeString();

                                        if($objeftAcount->save()){
                                            $objSequenceValueItem= SequenceValueItem::where('seq_name','PaymentMethod');
                                            $objSequenceValueItem->update(['seq_id' => $seqNextPaymentMethod+1]);
                                        }

                                    }

                                }

                                if(isset($request->tipo_impuesto_renta) && isset($request->tipo_impuesto_iva) && $request->tipo_documento == 'INVOICE_HONORARIOS'){

                                    $retIva= explode('*',$request->tipo_impuesto_iva);
                                    $retRenta= explode('*',$request->tipo_impuesto_renta);
                                    $store = ProductStore::where('type_store','MATRIZ')->first();

                                    PartyProfileDefault::updateOrCreate(
                                        ['party_id' => $request->id_empleado],
                                        [
                                            'product_store_id' => $store->product_store_id ,
                                            'party_id' => $request->id_empleado,
                                            'default_pay_meth' => 'EFT_ACCOUNT',
                                            'ret_ir_id' => $retRenta[1],
                                            'ret_iva_id' => $retIva[1],
                                            'last_updated_stamp' => now()->format('Y-m-d H:i:s'),
                                            'last_updated_tx_stamp' => now()->format('Y-m-d H:i:s'),
                                            'created_stamp' => now()->format('Y-m-d H:i:s'),
                                            'created_tx_stamp' => now()->format('Y-m-d H:i:s')
                                        ]
                                    );

                                }else{
                                    PartyProfileDefault::where('party_id',$request->id_empleado)->delete();
                                }

                                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                                            El contrato se ha guardado con exito!
                                        </div>';
                                $status = 1;

                                $existPartyRoleEmpleado = PartyRole::where([
                                    ['party_id',$request->id_empleado],
                                    ['role_type_id','EMPLOYEE']
                                ])->exists();
                                if(!$existPartyRoleEmpleado){
                                    $partyRole = new PartyRole;
                                    $partyRole->party_id=$request->id_empleado;
                                    $partyRole->role_type_id ='EMPLOYEE';
                                    $partyRole->status = true;
                                    $partyRole->save();
                                }

                            }
                        }
                    }
                }
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
           $msg = '<div class="alert alert-danger">' .
               '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
               '<ul>' .
               $errores .
               '</ul>' .
               '</div>';
           $status = 0;
       }
       return response()->json(array('status'=>$status,'msg'=>$msg));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $objContrato = Contrato::where('id_tipo_contrato',$request->id_tipo_contrato)
        ->select('cuerpo_contrato')->first();


        $dataEmpleados = PartyRole::join('person as p', 'party_role.party_id','=','p.party_id')
            ->where('party_role.role_type_id','EMPLOYEE')
            ->select('p.first_name','p.last_name','p.party_id')->get();

        $a = [];
        $b = [];
        $c = [];
        foreach ($dataEmpleados as $data){

            $contrataciones = ForeginContrataciones::where('contrataciones.party_id',$data->party_id)
            ->join('person as p', 'contrataciones.party_id','p.party_id')->where('estado',1)->get();

            /*if($contrataciones->count() != 2){

                for($i=0;$i<$contrataciones->count();$i++):
                    if($contrataciones[$i]->id_tipo_contrato_descripcion != tipoContratoDescripcion($request->id_tipo_contrato)->id_tipo_contrato_descripcion)
                        $a[]= ['nombre'=> $contrataciones[$i]->first_name." ".$contrataciones[$i]->last_name,'id_empleado'=>$contrataciones[$i]->party_id];
                endfor;
            }*/

            if($contrataciones->count() < 1)
                $b[] =  ['nombre'=> $data->first_name ." ". $data->last_name,'id_empleado'=>$data->party_id];

            $c = array_merge($a,$b);

        }

        $tipoContratoDescripcion = tipoContratoDescripcion($request->id_tipo_contrato);

        return response()->json([
            'tipo_contrato_descripcion' => $tipoContratoDescripcion->id_tipo_contrato_descripcion,
            'relacion_dependencia'      => $tipoContratoDescripcion->relacion_dependencia,
            'body'                      => $objContrato->cuerpo_contrato,
            'dataEmpleados'             => $c,
        ]);
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
    public function destroy($id)
    {
        //
    }

    public function inputsEmpleados(Request $request){

        $dataEmpleado = PartyRole::where([
            ['party_role.role_type_id','EMPLOYEE'],
            ['party_role.party_id',$request->party_id]
        ])->join('person as p', 'party_role.party_id','=','p.party_id')
            ->join('party_identification as pi','p.party_id','=','pi.party_id')
            ->join('party_contact_mech as pcm', 'p.party_id','=','pcm.party_id')
            ->join('contact_mech as cm','pcm.contact_mech_id','=','cm.contact_mech_id');

        $existPartyRelationship = PartyRelationShip::where('party_id_from',$request->party_id)->first();

        if($existPartyRelationship!=null){
            $dataEmpleado->join('party_relationship as prs','party_role.party_id','prs.party_id_from')
                ->join('person as per', 'prs.party_id_to','=','per.party_id')
                ->join('party_contact_mech as pcmc', 'per.party_id','=','pcmc.party_id');
        }

        $dataEmpleado->select(
                'p.party_id',
                'p.first_name',
                'p.first_name',
                'p.last_name',
                'p.nacionalidad',
                'party_role.role_type_id',
                'p.gender',
                'p.birth_date',
                'pi.party_identification_type_id',
                'pi.id_value',
                'cm.info_string',
                ($existPartyRelationship!=null ? 'per.party_id as party_id_contact' : 'cm.info_string' ),
                ($existPartyRelationship!=null ? 'per.first_name as first_name_contact' : 'cm.info_string' ),
                ($existPartyRelationship!=null ? 'per.last_name as last_name_contact' : 'cm.info_string' )
            );

        return view('layouts.views.contrataciones.partials.inputs_datos_nuevo_empleado',
            [
                'dataTipoIdentificacionGrupo' => TipoIdentificacionGrupo::select('party_identification_type_id','description')->get(),
                'dataProvinicias' =>Geo::where([
                    ['geo_type_id','PROVINCE'],
                    ['geo_id', 'like', 'EC%']
                ])->get(),
                'vista' => $request->vista,
                'dataEmpleados' => $dataEmpleado->first()

            ]);
    }

    public function formTerminacionContrato(Request $request){

        $contratacion = Contrataciones::where('id_contrataciones',$request->id_contrato)
            ->join('tipo_contrato as tc','contrataciones.id_tipo_contrato','tc.id_tipo_contrato')->select('relacion_dependencia')->first();


        $contratacion->relacion_dependencia
            ? $motivosAnulaciones= MotivoAnulacion::where('calcula_liquidacion',true)->orWhere('calcula_liquidacion',false)->get()
            : $motivosAnulaciones= MotivoAnulacion::where([
                ['calcula_liquidacion',false],
                ['desahucio',false],
                ['despido_intempestivo', false]
            ])->orWhere([
                ['calcula_liquidacion',true],
                ['desahucio',false],
                ['despido_intempestivo', false]
            ])->get();

        return view('layouts.views.contrataciones.partials.form_anulacion_contrato',[
                'dataMotivoAnulacion'=> $motivosAnulaciones,
                'idContrato'=>$request->id_contrato,
                'relacion_dependencia' =>  $contratacion->relacion_dependencia
        ]);
    }

    public function terminarContrato(Request $request)
    { //  dd($request->all());
        $valida = Validator::make($request->all(),[
            'id_motivo_anulacion' => 'required',
            'fecha_terminacion'=> 'required'
        ],[
            'id_motivo_anulacion.required'=>'Debe seleccionar un motivo para la terminación del contrato',
            'fecha_terminacion.required' => "Debe seleccionar la fecha de terminación de la contratación"
        ]);

        if (!$valida->fails()) {

            try{

                DB::beginTransaction();

                $dataMotivoTerminacion = MotivoAnulacion::where('id_motivo_anulacion',$request->id_motivo_anulacion)->first();

                if($dataMotivoTerminacion->calcula_liquidacion){

                    $dataContratacion = Contrataciones::where([
                        ['contrataciones.id_contrataciones',$request->id_contrataciones],
                        ['contrataciones.estado',1],
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

                    $ultimoSalario = Nomina::where('id_empleado',$dataContratacion->id_empleado)->select('total')->get()->last();

                    $dataPerson = Person::where('person.party_id',$dataContratacion->id_empleado)
                    ->join('party_identification as pi','person.party_id','pi.party_id')
                    ->join('party_identification_type as pit','pi.party_identification_type_id','pit.party_identification_type_id')
                    ->select('first_name','last_name','id_value','pit.description','person.party_id')->first();

                    $montoHorasExtras = "N/A";
                    $montoDecimoTercerSueldo = "N/A";
                    $montoDecimoCuartoSueldo = "N/A";
                    $montoVacaciones = "N/A";
                    $montoDesahucio = "N/A";
                    $montoSalario = 0.00;
                    $montoDespidoIntempestivo = "N/A";
                    $bono25 ='N/A';
                    $vistoBueno ='N/A';
                    $despidoIneficaz='N/A';
                    $indemnizacionDiscapacidad ='N/A';
                    $terminacionAntesPlazo = 'N/A';
                    $iva = 'N/A';
                    $retencionIva='N/A';
                    $retencionRenta='N/A';
                    $aportePersonal='N/A';
                    $montoBonosFijos = 0.00;

                    ////////// SALARIO //////////
                    $existNomina = Nomina::where([
                        ['id_empleado',$dataContratacion->id_empleado],
                        ['id_contrataciones',$dataContratacion->id_contrataciones]
                    ])->get();

                    if(count($existNomina)){
                        $diasTrabajadosMesActual = Carbon::parse($request->fecha_terminacion)->diffInDays(Carbon::parse($request->fecha_terminacion)->format('Y-m-01'))+1;
                    }else{

                        //$finMesInicioContratacion = Carbon::parse($dataContratacion->fecha_expedicion_contrato)->endOfMonth()->format('d');

                        $diasTrabajadosMesActual =  (int)Carbon::parse($request->fecha_terminacion)->diffInDays($dataContratacion->fecha_expedicion_contrato)+1;
                        /* if($finMesInicioContratacion < 31){
                            $diasTrabajadosMesActual =  (int)Carbon::parse($request->fecha_terminacion)->diffInDays($dataContratacion->fecha_expedicion_contrato)+1;
                        }else{
                            $diasTrabajadosMesActual =  (int)Carbon::parse($request->fecha_terminacion)->diffInDays($dataContratacion->fecha_expedicion_contrato)-1;
                        }*/

                    }

                    $mesTerminacionContrato = Carbon::parse($request->fecha_terminacion)->format('m');

                    if(($mesTerminacionContrato == "02" && $diasTrabajadosMesActual == 28) || (Carbon::parse($request->fecha_terminacion)->format('m') == "02" && $diasTrabajadosMesActual == 29)){
                        $dias = 30;
                    }else{
                        $dias = $diasTrabajadosMesActual == 31 ? 30 : $diasTrabajadosMesActual;
                    }

                    if(!$dataContratacion->relacion_dependencia && $dias < 30){

                        $i = Carbon::parse($request->fecha_terminacion)->format('Y-m-01');
                        $f = Carbon::parse($request->fecha_terminacion)->format('Y-m-d');
                        $s =true;
                        $dias = 0;

                        while($s){

                            $i = Carbon::parse($i)->addDay();

                            if($i->format('Y-m-d') >= $f) $s =false;

                            if($i->isWeekday()) $dias++;

                        }

                    }

                    $ultimaNomina = Nomina::where([
                        ['id_empleado',$dataContratacion->id_empleado],
                        ['id_contrataciones',$dataContratacion->id_contrataciones],
                    ])->select('fecha_nomina')->orderBy('id_nomina','desc')->first();

                    if(isset($ultimaNomina)){

                        $mesUltimaNomina = Carbon::parse($ultimaNomina->fecha_nomina)->format('m');

                        if($mesUltimaNomina < $mesTerminacionContrato)
                            $montoSalario = number_format(($dataContratacion->salario/30) * $dias,2,".","");

                    }else{


                        $montoSalario = number_format(($dataContratacion->salario/30) * $dias,2,".","");

                    }
                    if(isset($request->montoSalario)) $montoSalario = $request->montoSalario;

                    ////////// FIN SALARIO //////////


                    ////////// OTROS CÁCULOS //////////
                    $getHorasExtras = getHorasExtras($dataContratacion->id_empleado, $request->store,$dataContratacion->fecha_expedicion_contrato,$request->fecha_terminacion);
                    $montoHorasExtras = isset($request->montoHorasExtras) ? $request->montoHorasExtras : number_format($getHorasExtras,2,".","");

                    //$montoHorasExtras=0.00;
                    $comisiones = Comisiones::where([
                        ['id_empleado',$dataContratacion->id_empleado],
                        ['pagada',0]
                    ])->join('tipo_comisiones as tc','comisiones.id_tipo_comision','tc.id_tipo_comision')->get();

                    $montoComisiones = 0.00;
                    foreach ($comisiones as $comision){
                        $montoComisiones += number_format($comision->cantidad,2,".","");

                        if($request->store == 1) {
                            $objComsiones = Comisiones::find($comision->id_comisiones);
                            $objComsiones->pagada = 1;
                            $objComsiones->save();
                        }
                    }
                    if(isset($request->montoComisiones)) $montoComisiones = $request->montoComisiones;

                    //--------------------------//
                    $consumos = Consumos::where([
                        ['id_empleado',$dataContratacion->id_empleado],
                        ['estado',0],
                    ])->get();

                    $montoConsumos = 0.00;
                    foreach ($consumos as $c) {
                        $montoConsumos += number_format($c->monto_descuento,2,".","");

                        $objConsumo = Consumos::find($c->id_consumo);
                        $objConsumo->estado = 1;
                        $objConsumo->save();
                    }
                    if(isset($request->montoConsumos)) $montoConsumos = $request->montoConsumos;
                    //--------------------------//

                    //--------------------------//
                    $anticipos = Anticipos::where([
                        ['id_empleado',$dataContratacion->id_empleado],
                        ['estado',1],
                        ['descontado',0]
                    ])->get();

                    $arrAnticipos =[];
                    $montoAnticipos = 0.00;
                    foreach ($anticipos as $a){
                        $montoAnticipos += number_format($a->cantidad,2,".","");

                        if($request->store == 1) {
                            $objAnticipos = Anticipos::find($a->id_anticipo);
                            $objAnticipos->descontado = 1;
                            $objAnticipos->save();
                        }

                        $arrAnticipos[] = $a;
                    }
                    if(isset($request->montoAnticipos)) $montoAnticipos = $request->montoAnticipos;
                    //--------------------------//

                    //--------------------------//
                    $montoDescuentos = 0.00;
                    $otrosDescuentos = OtrosDescuentos::where([
                        ['id_empleado',$dataContratacion->id_empleado],
                        ['descontado',0]
                    ])->get();

                    $arrOtrosDescuentos = [];
                    foreach($otrosDescuentos as $descuento){
                        $montoDescuentos += $descuento->cantidad;

                        if($request->store == 1) {
                            $objDecuentos = OtrosDescuentos::find($descuento->id_descuento);
                            $objDecuentos->descontado = 1;
                            $objDecuentos->save();
                        }
                        $arrOtrosDescuentos[] = $descuento;

                    }
                    if(isset($request->montoDescuentos)) $montoDescuentos = $request->montoDescuentos;
                    //--------------------------//

                    ////////// FIN OTROS CÁCULOS //////////


                    $diasTrabajados = Carbon::parse($dataContratacion->fecha_expedicion_contrato)->diffInDays($request->fecha_terminacion);
                    $annosCompletosTrabajados = round(($diasTrabajados/365), 0, PHP_ROUND_HALF_DOWN);

                    $remuneracion = $montoHorasExtras != "N/A" ? ($dataContratacion->salario + $montoHorasExtras) : $dataContratacion->salario;

                    $arrBonosFijos   = getBonosFijos($dataContratacion->id_contrataciones,true,$dias,Carbon::parse($request->fecha_terminacion)->toDateString());
                    $arrPrestamos    = getPrestamos($dataContratacion->id_contrataciones,$request->store,true,Carbon::parse($request->fecha_terminacion)->toDateString());

                    if($dataContratacion->relacion_dependencia && isset($ultimoSalario->total)){

                        /// BONO 25% /////
                        if($request->bono_25_porciento== "true"){
                            $bono25 = isset($request->bono25) ? $request->bono25 : ($remuneracion / 4 * $annosCompletosTrabajados);

                        }
                        /// BONO 25% /////


                        //// VISTO BUENO ////
                        if($request->visto_bueno== "true")
                            $vistoBueno = isset($request->vistoBueno) ?  $request->vistoBueno : $remuneracion*12;
                        //// FIN VISTO BUENO ////


                        //// DESPIDO INEFICAZ ////
                        if($request->despido_ineficaz== "true")
                            $despidoIneficaz = isset($request->despidoIneficaz) ? $request->despidoIneficaz : $remuneracion*12;
                        //// FIN DESPIDO INEFICAZ ////


                        ///// INDEMNIZACION POR DISCAPACIDAD  /////
                        if($request->indemnizacion_discapacidad== "true")
                            $indemnizacionDiscapacidad = isset($request->indemnizacionDiscapacidad) ? $request->indemnizacionDiscapacidad : $remuneracion*6;
                        ///// FIN INDEMNIZACION POR DISCAPACIDAD  /////

                        /////// DECIMO TERCER SUELDO ///////

                        $totalSueldosDecimoTercero = 0.00;

                        $fechaInicioCalculo = Carbon::parse($request->fecha_terminacion)->subYear(1)->format('Y-12-01');
                        $fechaFinCalculo = Carbon::parse($request->fecha_terminacion)->endOfMonth()->format('Y-m-d');

                        $dataNomina = Nomina::where('id_empleado',$dataContratacion->id_empleado)
                        ->whereBetween('fecha_nomina',[$fechaInicioCalculo,$fechaFinCalculo])
                        ->select('id_nomina','total','fecha_nomina')->get();

                        foreach ($dataNomina as $x=> $dN){

                            $decimoAcumulado = DecimoTercero::where('id_nomina',$dN->id_nomina)->where('estado',false)->first();

                            if(isset($decimoAcumulado)){
                                if($request->store != 0){
                                    $decimoAcumulado->estado=true;
                                    $decimoAcumulado->save();
                                }
                                //$nominaPasada = NominasPasadas::where('id_nomina',$dN->id_nomina)->first();
                                $totalSueldosDecimoTercero += $decimoAcumulado->cantidad;
                            }


                        }
                        //$totalSueldosDecimoTercero+= $dataContratacion->salario;

                        $montoDecimoTercerSueldo = isset($request->montoDecimoTercerSueldo) ?  $request->montoDecimoTercerSueldo : number_format(($totalSueldosDecimoTercero+($montoSalario/12)),2,".","");

                        /////// FIN DECIMO TERCER SUELDO ///////

                        /////// DECIMO CUARTO SUELDO ///////

                        $fechaInicioCalculo = Carbon::parse($request->fecha_terminacion)->subYear(1)->format('Y-08-01');

                        //SI EMPEZÓ SU CONTRATO DESPUES DE INICIADO EL PERIODO DE CÁLCULO 01-08-XX
                        if($dataContratacion->fecha_expedicion_contrato > $fechaInicioCalculo)
                            $fechaInicioCalculo = $dataContratacion->fecha_expedicion_contrato;

                        $fechaFinCalculo = Carbon::parse($request->fecha_terminacion)->format('Y-m-d');
                        $dataConfiguracionEmpresa = ConfiguracionVariablesEmpresa::select('sueldo_basico_unificado_vigente','aporte_personal')->first();
                        $mesTrabajadosUltimoAnno = Carbon::parse($fechaFinCalculo)->diffInMonths($fechaInicioCalculo);

                        if($dataContratacion->fecha_expedicion_contrato > Carbon::parse($dataContratacion->fecha_expedicion_contrato)->format('Y-m-01')){

                            $parcialDias = Carbon::parse($dataContratacion->fecha_expedicion_contrato)->endOfMonth()->diffInDays($dataContratacion->fecha_expedicion_contrato);
                            $mesTrabajadosUltimoAnno = $mesTrabajadosUltimoAnno+($parcialDias/30);

                        }

                        $diasTrabajadosUltimoAnno = $mesTrabajadosUltimoAnno*30;

                        //SI EL CONTRATO NO TERMINA EL ÚLTIMO DEL MES
                        if($request->fecha_terminacion < Carbon::parse($request->fecha_terminacion)->endOfMonth()->format('Y-m-d')){
                            $diasTrabajadosUiltmoMes = Carbon::parse(Carbon::parse($fechaFinCalculo)->format('Y-m-01'))->diffInDays($fechaFinCalculo)+1;
                            $diasTrabajadosUltimoAnno += $diasTrabajadosUiltmoMes;
                        }

                        $montoDecimoCuartoSueldo = isset($request->montoDecimoCuartoSueldo) ? $request->montoDecimoCuartoSueldo : number_format(($dataConfiguracionEmpresa->sueldo_basico_unificado_vigente/360)*$diasTrabajadosUltimoAnno,2,".","");

                        //FIN DECIMO CUARTO SUELDO

                       // dd($dataConfiguracionEmpresa->sueldo_basico_unificado_vigente,$mesTrabajadosUltimoAnno,$diasTrabajadosUltimoAnno, $diasTrabajadosUiltmoMes,$fechaInicioCalculo,$fechaFinCalculo);
                        /////// FIN DECIMO CUARTO SUELDO ///////


                        /////// VACACIONES NO GOZADAS ////////

                        /* $dataVacaciones = Vacaciones::where([
                            ['id_empleado',$dataContratacion->id_empleado],
                            ['estado',3]
                        ])->select('fecha_fin')->get()->last(); */

                        /* $nominasVacaciones = Nomina::whereBetween('fecha_nomina',[$dataContratacion->fecha_expedicion_contrato,Carbon::parse($request->fecha_terminacion)->endOfMonth()->format('Y-m-d')])
                            ->where('id_empleado',$dataContratacion->id_empleado)->pluck('id_nomina')->toArray();

                        $dataVacaciones = VacacionesNomina::whereIn('id_nomina',$nominasVacaciones)->where('estado',false)->sum('cantidad'); */

                        $fechaFinCalculo = Carbon::parse($request->fecha_terminacion)->endOfMonth()->format('Y-m-d');

                        $dataNomina = Nomina::where('id_empleado',$dataContratacion->id_empleado)
                            ->whereBetween('fecha_nomina',[$dataContratacion->fecha_expedicion_contrato,$fechaFinCalculo])
                            ->select('id_nomina')->get();

                        $totalSueldosVacaciones = 0.00;

                        foreach ($dataNomina as $dN){

                            $vacacionNomina = VacacionesNomina::where([
                                ['id_nomina',$dN->id_nomina],
                                ['estado',false]
                            ])->first();

                            if(isset($vacacionNomina)){
                                 $totalSueldosVacaciones += $vacacionNomina->cantidad;

                                if($request->store==1){
                                    $vn = VacacionesNomina::find($vacacionNomina->id_vacaciones_nomina);
                                    $vn->update(['estado'=>true]);
                                }
                            }


                        }

                        $totalSueldosVacaciones += is_numeric($montoHorasExtras) ?(($montoSalario+$montoHorasExtras)/24) : ($montoSalario/24);
                        $montoVacaciones = isset($request->montoVacaciones) ? $request->montoVacaciones : number_format($totalSueldosVacaciones,2,".","");

                        /////// FIN VACACIONES NO GOZADAS ////////


                        //////////// DESAHUCIO /////////////
                        if($dataMotivoTerminacion->desahucio)
                            $montoDesahucio = isset($request->montoDesahucio) ?  $request->montoDesahucio : number_format((($montoHorasExtras != "N/A" ? ($dataContratacion->salario+$montoHorasExtras) : $dataContratacion->salario)/4)*$annosCompletosTrabajados,2,".","");

                        //////////// FIN DESAHUCIO /////////////

                        //////////// DESPIDO INTEMPESTIVO ///////////
                        if($dataMotivoTerminacion->despido_intempestivo) {
                            $annosRedondeado = round(($diasTrabajados/365), 0, PHP_ROUND_HALF_UP);
                            ($annosRedondeado < 4)
                                ? $montoDespidoIntempestivo = number_format((($montoHorasExtras != "N/A" ? ($dataContratacion->salario+$montoHorasExtras) : $dataContratacion->salario) * 3),2,".","")
                                : $montoDespidoIntempestivo = number_format((($montoHorasExtras != "N/A" ? ($dataContratacion->salario+$montoHorasExtras) : $dataContratacion->salario) * $annosRedondeado),2,".","");

                            if(isset($request->montoDespidoIntempestivo)) $montoDespidoIntempestivo = $request->montoDespidoIntempestivo;

                        }
                        //////////// FIN DESPIDO INTEMPESTIVO ///////////


                        //////////// APORTE PERSONAL /////////////
                        $aportePersonal = number_format(($montoSalario *($dataConfiguracionEmpresa->aporte_personal/100)),2);
                        //////////// FIN APORTE PERSONAL /////////////

                    }else{

                        if($request->terminación_antes_plazo=="true"){
                            $diasFaltantes = Carbon::parse($dataContratacion->fecha_expiracion_contrato)->diffInDays($request->fecha_terminacion)+1;
                            $terminacionAntesPlazo = ($remuneracion/2)*$diasFaltantes;
                        }

                    }

                    $ingresos = $montoSalario + $montoComisiones + (is_numeric($montoDecimoTercerSueldo) ? $montoDecimoTercerSueldo : 0) +(is_numeric($montoDecimoCuartoSueldo) ? $montoDecimoCuartoSueldo : 0) + (is_numeric($montoVacaciones) ? $montoVacaciones : 0) + (is_numeric($montoDesahucio) ? $montoDesahucio : 0) + (is_numeric($montoDespidoIntempestivo) ? $montoDespidoIntempestivo : 0) + (is_numeric($montoHorasExtras) ? $montoHorasExtras : 0) + ($arrBonosFijos['montoBonosFijos']) + (is_numeric($bono25) ? $bono25 : 0) + (is_numeric($vistoBueno) ? $vistoBueno : 0) + (is_numeric($despidoIneficaz) ? $despidoIneficaz : 0) + (is_numeric($indemnizacionDiscapacidad) ? $indemnizacionDiscapacidad : 0);
                    $egresos  = $montoConsumos + $montoAnticipos + $montoDescuentos + $arrPrestamos['montoPrestamos'] + (is_numeric($aportePersonal) ? $aportePersonal : 0);
                    $subTotal = $ingresos - $egresos;
                    $total = $subTotal;

                    if(!$dataContratacion->relacion_dependencia){
                        $iva            = $dataContratacion->retencion_iva > 0 ? ( isset($request->iva) ? $request->iva : ($montoSalario + $montoComisiones+ ($montoHorasExtras!='N/A' ? $montoHorasExtras : 0))*($dataContratacion->iva/100) ) : 0;
                        $retencionIva   = $dataContratacion->retencion_iva > 0 ? ( isset($request->retencionIva) ? $request->retencionIva : ($iva *($dataContratacion->retencion_iva/100)) ) : 0;
                        $retencionRenta = $dataContratacion->retencion_renta > 0 ? ( isset($request->retencionRenta) ? $request->retencionRenta : ($montoSalario + $montoComisiones + ($montoHorasExtras!='N/A' ? $montoHorasExtras : 0))*($dataContratacion->retencion_renta/100) ) : 0;
                        $total          = $subTotal + $iva - $retencionIva - $retencionRenta;
                    }

                    $dataLiquidacion = [
                        'nombreEmpleado'          => $dataPerson->first_name . " " . $dataPerson->last_name,
                        'documento'               => $dataPerson->description,
                        'identificacion'          => $dataPerson->id_value,
                        'cargo'                   => $dataContratacion->nombre,
                        'idContrato'              => $dataContratacion->id_contrataciones,
                        'montoDecimoTercerSueldo' => $montoDecimoTercerSueldo,
                        'montoDecimoCuartoSueldo' => $montoDecimoCuartoSueldo,
                        'montoVacaciones'         => $montoVacaciones,
                        'montoDesahucio'          => $montoDesahucio,
                        'montoDespidoIntempestivo'=> $montoDespidoIntempestivo,
                        'montoHorasExtras'        => $montoHorasExtras,
                        'montoComisiones'         => $montoComisiones,
                        'arrPrestamos'            => $arrPrestamos['arrPrestamos'],
                        'arr_bonos_fijos'         => $arrBonosFijos['arrBonosFijos'],
                        'montoConsumos'           => number_format($montoConsumos,2,".",""),
                        'montoAnticipos'          => $montoAnticipos,
                        'montoSalario'            => $montoSalario,
                        'montoDescuentos'         => $montoDescuentos,
                        'diasTrabajadosMesActual' => $dias,
                        'iva'                     => $iva,
                        'retencionRenta'          => $retencionRenta,
                        'retencionIva'            => $retencionIva,
                        'montoTotalIngresos'      => $ingresos,
                        'montoTotalEgresos'       => $egresos,
                        'montoTotalARecibir'      => $total,
                        'bono25'                  => $bono25,
                        'vistoBueno'              => $vistoBueno,
                        'despidoIneficaz'         => $despidoIneficaz,
                        'indemnizacionDiscapacidad' => $indemnizacionDiscapacidad,
                        'terminacionAntesPlazo' => $terminacionAntesPlazo,
                        'aportePersonal' => $aportePersonal,
                        'fechaLiquidacion' => $request->fecha_terminacion
                    ];

                    if($request->store == 0) {
                        return view('layouts.views.contrataciones.partials.vista_liquidacion', [
                            'dataLiquidacion' => $dataLiquidacion,
                            'id_motivo_anulacion' => $request->id_motivo_anulacion,
                            'fechaTerminacion' => $request->fecha_terminacion,
                            'bono_25_porciento'=> $request->bono_25_porciento== "true"
                        ]);

                    }else{

                        $status = 0;
                        $objFinalizacionContratacion = new FinalizacionContratacion;
                        $objFinalizacionContratacion->id_contrataciones = $request->id_contrataciones;
                        $objFinalizacionContratacion->id_tipo_finalizacion = $request->id_motivo_anulacion;
                        $objFinalizacionContratacion->fecha_finalizacion = $request->fecha_terminacion;

                        if ($objFinalizacionContratacion->save()) {

                            $modelFinalizacionContratacion = FinalizacionContratacion::all()->last();

                            $objForeignContrataciones = ForeginContrataciones::find($request->id_contrataciones);
                            $objForeignContrataciones->estado = 3;

                            if ($objForeignContrataciones->save()) {

                                $objContrataciones = Contrataciones::find($request->id_contrataciones);
                                $objContrataciones->estado = 3;

                                if ($objContrataciones->save()) {

                                    $dataContratacionConfidencialidad = Contrataciones::where([
                                        ['id_empleado',$dataPerson->party_id],
                                        ['id_tipo_contrato_descripcion',1],
                                        ['estado',1]
                                    ])->first();

                                    if(isset($dataContratacionConfidencialidad->id_contrataciones) && $dataContratacionConfidencialidad->id_contrataciones != null){

                                        $objContratacionesConfidencialidad = Contrataciones::find($dataContratacionConfidencialidad->id_contrataciones);
                                        $objContratacionesConfidencialidad->estado = 3;
                                        $objContratacionesConfidencialidad->save();

                                        $objForeginContratacionesConfidencialidad = ForeginContrataciones::find($dataContratacionConfidencialidad->id_contrataciones);
                                        $objForeginContratacionesConfidencialidad->estado = 3;
                                        $objForeginContratacionesConfidencialidad->save();

                                    }

                                    $view = \View::make('layouts.views.nomina.partials.rol_pago_liquidacion', compact('dataLiquidacion'))->render();
                                    $pdf = \App::make('dompdf.wrapper');
                                    $pdf->loadHTML($view);
                                    $nombre_archivo = $request->fecha_terminacion."_liquidacion_".$dataLiquidacion['identificacion']."_".$dataLiquidacion['nombreEmpleado'].".pdf";
                                    $pdf->save(public_path('roles_pago') . '/'.$nombre_archivo);

                                    $idEmpleado = Contrataciones::where('id_contrataciones',$dataLiquidacion['idContrato'])->select('id_empleado')->first()->id_empleado;

                                    $objImagenRoles = new ImagenesRoles;
                                    $objImagenRoles->fecha_nomina  = Carbon::parse($request->fecha_terminacion)->format("Y-m-05");
                                    $objImagenRoles->nombre_imagen = $nombre_archivo;
                                    $objImagenRoles->id_empleado   = $idEmpleado;
                                    $objImagenRoles->tipo          = 2;

                                    if($objImagenRoles->save()){

                                        $idNomina= Nomina::orderBy('id_nomina','desc')->select('id_nomina')->first();

                                        $objNomina = new Nomina;
                                        $objNomina->id_nomina =isset($idNomina) ? $idNomina->id_nomina+1 : 1;
                                        $objNomina->id_empleado  = $idEmpleado;
                                        $objNomina->fecha_nomina = Carbon::parse($request->fecha_terminacion)->format('Y-m-05');
                                        $objNomina->total        = number_format($dataLiquidacion['montoTotalARecibir'],2,".","");
                                        $objNomina->id_contrataciones = $request->id_contrataciones;
                                        $objNomina->persona = $dataPerson->first_name . " " . $dataPerson->last_name;
                                        $objNomina->identificacion = $dataPerson->id_value;
                                        $objNomina->liquidacion = true;

                                        if($objNomina->save()){

                                            if($request->store == 1){

                                                $model = Nomina::orderBy('id_nomina','desc')->first();
                                                $classNomina = new NominaController;

                                                if($dataContratacion->relacion_dependencia && isset($ultimoSalario->total)){

                                                    $invoice = $classNomina->generaFacturaRelacionDependencia([
                                                        'contrataciones' => $dataContratacion,
                                                        'date' => Carbon::parse($request->fecha_terminacion),
                                                        'base' => $montoSalario,
                                                        'horasExtras' => $montoHorasExtras,
                                                        'bonos' => $arrBonosFijos['montoBonosFijos'],
                                                        'comisiones' => $montoComisiones,
                                                        'fondoReserva' => 0,
                                                        'decimoTercero' => is_numeric($montoDecimoTercerSueldo) ? $montoDecimoTercerSueldo : 0,
                                                        'decimoCuarto' => is_numeric($montoDecimoCuartoSueldo) ? $montoDecimoCuartoSueldo : 0,
                                                        'vacaciones' => is_numeric($montoVacaciones) ? $montoVacaciones :0,
                                                        'prestamos' => $arrPrestamos['arrPrestamos'],
                                                        'anticipos' => $arrAnticipos,
                                                        'total' => $objNomina->total,
                                                        'otrosDescuentos' => $arrOtrosDescuentos,
                                                        'aportePersonalIESS' => $aportePersonal,
                                                        'descripcion_invoice' => 'FACTURA DE LIQUIDACIÓN GENERADA DESDE EL MÓDULO DE NÓMINA',
                                                        'descripcion_invoice_item' => 'LIQUIDACIÓN DE INGRESOS BASE',
                                                        'descripcion_hora_extra' => 'LIQUIDACIÓN DE HORAS EXTRAS',
                                                        'descripcion_bono' => 'LIQUIDACIÓN DE BONOS',
                                                        'descripcion_comision'=> 'LIQUIDACIÓN DE COMISIONES',
                                                        'descripcion_fondo_reserva' => 'LIQUIDACIÓN DE FONDO DE RESERVA',
                                                        'descripcion_dcmo_3er' => 'LIQUIDACIÓN DE DECIMO TERCER SUELDO',
                                                        'descripcion_dcmo_4to' => 'LIQUIDACIÓN DE DECIMO CUARTO SUELDO',
                                                        'descripcion_ancticipo' => 'LIQUIDACIÓN DE DESCUENTO DE ANTICIPO SUELDO',
                                                        'bono_25' => $bono25
                                                    ]);

                                                }else{

                                                    $invoice = $classNomina->generaFacturaHonorarios([
                                                        'id_nomina' => $model->id_nomina,
                                                        'honorarios' => number_format($dataLiquidacion['montoTotalIngresos'],2,".",""),
                                                        'contrataciones' => $dataContratacion,
                                                        'date' => Carbon::parse($request->fecha_terminacion),
                                                        'iva' => $iva,
                                                        'prestamos' => $arrPrestamos['arrPrestamos'],
                                                        'anticipos' => $arrAnticipos,
                                                        'otrosDescuentos' => $arrOtrosDescuentos,
                                                        'descripcion_invoice_item' => 'PAGO DE INGRESOS DE NÓMINA'
                                                    ]);

                                                    $invoice['invoiceId'] = implode(',',$invoice['invoiceId']);

                                                }

                                            }
                                        }

                                        //dd($invoice);

                                        $objNomina2 = Nomina::find($model->id_nomina);
                                        $objNomina2->update([
                                            'id_factura' => $invoice['invoiceId']
                                        ]);

                                        DB::commit();
                                        flash('<i class="fa fa-exclamation-circle" aria-hidden="true"></i> La liquidación se ha generado con exito y a continuación se muestra el rol de pago')->success();

                                        return view('layouts.views.nomina.partials.rol_pago_liquidacion',[
                                            'dataLiquidacion' => $dataLiquidacion
                                        ]);



                                    }
                                }
                            }
                        }
                    }

                }else{
                    $contratacion = Contrataciones::find($request->id_contrataciones);
                    $contratacion->update(['estado'=>3]);
                    $contratacion = ForeginContrataciones::find($request->id_contrataciones);
                    $contratacion->update(['estado'=>3]);
                    $finalizacionContratacion = new FinalizacionContratacion;
                    $finalizacionContratacion->id_contrataciones = $request->id_contrataciones;
                    $finalizacionContratacion->id_tipo_finalizacion = $request->id_motivo_anulacion;
                    $finalizacionContratacion->fecha_finalizacion = $request->fecha_terminacion;
                    $finalizacionContratacion->save();
                    DB::commit();
                }

                $status = 1;
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                        Se ha terminado la contratación con éxito
                    </div>';


            }catch(\Exception $e){

                $status = 0;
                $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                        ha ocurrido un error al intentar terminar el contrato, <br />'. $e->getMessage().'<br />'. $e->getFile().'<br />'.$e->getLine().'
                        </div>';
                DB::rollBack();

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
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
            $status = 0;
        }
        return response()->json(['status' => $status, 'msg' => $msg]);
    }

    public function anularContrato(Request $request)
    {
        $objForeignContrataciones = ForeginContrataciones::find($request->id_contrato);
        $objForeignContrataciones->estado = 2;

        if ($objForeignContrataciones->save()) {

            $objContrataciones = Contrataciones::find($request->id_contrato);
            $objContrataciones->estado = 2;

            if ($objContrataciones->save()) {

                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                          La contratación se ha anulado exitosamente
                        </div>';
                $status = 1;

            }else {
                $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                           ha ocurrido un error al intentar anular la contratación, intente nuevamente!
                        </div>';
                $status = 0;
            }
        }
        return response()->json(['status' => $status, 'msg' => $msg]);
    }

    public function addContratacion(Request $request){

        return view('layouts.views.contrataciones.partials.form_upload_contratacion_firmada',
            [
                'idContratacion'=> $request->id_contrataciones,
                'imagenesContrataciones' => ImagenesDetallesContrataciones::where('id_detalles_contrataciones',$request->id_contrataciones)->get(),
                'estadoContrato'=>Contrataciones::where('id_contrataciones',$request->id_contrataciones)->select('estado')->first(),
            ]);
    }

    public function uploadImagenContratacion(Request $request){

        $msg ='';
        $status = 0;

        foreach ($request->file as $image){

            $validaImagen = Validator::make($request->file,array(
                'mimeType' => 'image',
            ));

            if (!$validaImagen->fails()) {

                //$archivo = $request->file($image);
                $nombre_imagen = mt_rand() . '_' . mt_rand() . $image->getClientOriginalName();

                Storage::disk('imagenes_contratos')->put($nombre_imagen, \File::get($image));
            }

            $objImagenesDetallesContrataciones = new ImagenesDetallesContrataciones;
            $objImagenesDetallesContrataciones->id_detalles_contrataciones = $request->id_contratacion;
            $objImagenesDetallesContrataciones->imagen = $nombre_imagen;

            if($objImagenesDetallesContrataciones->save()){
                $msg .= '<div class="alert alert-success" role="alert" style="margin: 10px">
                            Se ha guardado la imagen '.$image->getClientOriginalName().' con exito
                        </div>';
                $status = 1;
            }else{
                $msg .= '<div class="alert alert-danger" role="alert" style="margin: 10px">
                            Hubo un error al trata de guardar la imagen con el nombre '.$image->getClientOriginalName().'
                        </div>';
                $status = 0;
            }
        }
        return response()->json(array('status'=>$status,'msg'=>$msg));
    }

    public function deleteImagenContratacion(Request $request){

       $deleteImagen = ImagenesDetallesContrataciones::find($request->id_imagen)->delete();

       if($deleteImagen){
           Storage::disk('imagenes_contratos')->delete($request->nombre_imagen);
           $msg = '<div class="alert alert-success" role="alert" style="margin: 10px">
                     Se ha eliminado la imagen con exito
               </div>';
           $status = 1;
       }else{
           $msg = '<div class="alert alert-alert" role="alert" style="margin: 10px">
                     Hubo un error al trata de eliminar la imagen, intente nuevamente
               </div>';
           $status = 1;
       }
       return response()->json(array('status'=>$status,'msg'=>$msg));

    }

    public function camposObligatorios(Request $request){

        return view('layouts.views.contrataciones.partials.campos_obligatorios', [
            'dataCargos' => Cargo::all(),
            'dataTipoContrato' => TipoContratos::where('id_tipo_contrato',$request->id_tipo_contrato)->select('caducidad')->first(),
        ]);

    }

    public function camposRelacionDependencia(){
        return view('layouts.views.contrataciones.partials.campos_relacion_dependecia');
    }

    public function camposSinRelacionDependencia (Request $request){
        return view('layouts.views.contrataciones.partials.campos_sin_relacion_dependencia',[
            'iva' => Iva::all(),
            'dataAddendum' => DetalleContratacion::where('id_detalle_contrataciones',$request->id_detalle_contratacion)
                                ->select('iva','retencion_iva','retencion_renta','tipo_documento')->first(),
            'tipoDocumentos'=> tipoDocumentosNomina(),
            'tipoImpuestos' => DeductionType::where('activo','S')->get()
        ]);
    }

    public function updateContratacion(Request $request){
       // dd(explode('*',$request->tipo_impuesto_iva));
        if($request->has('salario')){
            $valida =  Validator::make($request->all(), array(

                'salario'            => 'required',
                //'fecha_horario'    => 'required',
                'fecha_inicio'       => 'required',
                'horas'              => 'required',
                'id_cargo'           => 'required',
                'id_tipo_contrato'   => 'required',
                'nombres'            => 'required',
                'apellidos'          => 'required',
                'nacimiento'         => 'required',
                'genero'             => 'required',
                'tipo_identificacion'=> 'required',
                'identificacion'     => 'required',
                'correo'             => 'required|email',
                'telefono'           => 'required',
                //'nombre_contacto'    => 'required',
                //'apellido_contacto'  => 'required',
                //'telefono_contacto'  => 'required',
                'C_V'                => 'required',
                'nacionalidad'       => 'required'
            ));
        }else{
            $valida =  Validator::make($request->all(),array(
                'id_empleado'      => 'required',
                'id_tipo_contrato' => 'required',
            ));
        }

        if(!$valida->fails()) {

            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                        hubo un error al realizar la contratación, intente nuevamente
                </div>';
            $status = 0;

            $objContrato      = Contrato::where('id_tipo_contrato',$request->id_tipo_contrato)->first();
            $objConfigEmpresa = ConfiguracionEmpresa::all();

            if($request->has('salario')){

                $objPerson = Person::find($request->party_id);
                $objPerson->first_name           = $request->nombres;
                $objPerson->last_name            = $request->apellidos;
                $objPerson->gender               = $request->genero;
                $objPerson->birth_date           = $request->nacimiento;
                $objPerson->nacionalidad         = $request->nacionalidad;

                if($objPerson->save()){

                    $objIdentificacion = PartyIdentification::find($request->party_id);
                    $objIdentificacion->party_identification_type_id  = $request->tipo_identificacion;
                    $objIdentificacion->id_value                      = $request->identificacion;

                    if($objIdentificacion->save()){

                        $objPartyRole = PartyRole::find($request->party_id);
                        $objPartyRole->role_type_id = 'EMPLOYEE';
                        $objPartyRole->status=true;

                        if($objPartyRole->save()){

                            $dataPartyContactMech = PartyContactMech::where('party_id',$request->party_id)
                                ->join('contact_mech as cm','party_contact_mech.contact_mech_id','cm.contact_mech_id')->get();

                            foreach ($dataPartyContactMech as $pcm){
                                if($pcm->contact_mech_type_id == "EMAIL_ADDRESS")
                                    $id_contact_mech_email = $pcm->contact_mech_id;
                                if($pcm->contact_mech_type_id == "TELECOM_NUMBER")
                                    $id_contact_mech_number = $pcm->contact_mech_id;
                                if($pcm->contact_mech_type_id == "POSTAL_ADDRESS")
                                    $id_contact_mech_address = $pcm->contact_mech_id;
                            }

                            if(isset($id_contact_mech_email)){
                                $objContactMetch = ContactMetch::find($id_contact_mech_email);
                            }else{
                                $objContactMetch = new ContactMetch;
                                $seqNextContactMech = SequenceValueItem::where('seq_name','ContactMech')->select('seq_id')->first()->seq_id + 1;
                                $objSeq = SequenceValueItem::where('seq_name','ContactMech');
                                $objSeq->update(['seq_id'=>$seqNextContactMech]);
                                $objContactMetch->contact_mech_id = $seqNextContactMech;
                            }

                            $objContactMetch->contact_mech_type_id = 'EMAIL_ADDRESS';
                            $objContactMetch->info_string          =  $request->correo;

                            if(isset($id_contact_mech_number)){
                                $objTelecomNumber = TelecomNumber::find($id_contact_mech_number);
                            }else{
                                $objTelecomNumber = new TelecomNumber;
                                $seqNextContactMechTelecomNumber = SequenceValueItem::where('seq_name','ContactMech')->select('seq_id')->first()->seq_id + 1;
                                $objSeq = SequenceValueItem::where('seq_name','ContactMech');
                                $objSeq->update(['seq_id'=>$seqNextContactMechTelecomNumber]);
                                $objContactMetch = new ContactMetch;
                                $objContactMetch->contact_mech_id = $seqNextContactMechTelecomNumber;
                                $objContactMetch->contact_mech_type_id = "TELECOM_NUMBER";
                                $objContactMetch->save();
                                $objTelecomNumber->contact_mech_id = $seqNextContactMechTelecomNumber;
                            }

                            $objTelecomNumber->country_code    = '593';
                            $objTelecomNumber->contact_number  = $request->telefono;

                            if($objContactMetch->save() && $objTelecomNumber->save()){

                                if(!isset($id_contact_mech_email)) {
                                    $modelObjContactMetch = ContactMetch::all()->last();
                                    $objPartyContactMech = new PartyContactMech;
                                    $objPartyContactMech->party_id = $request->party_id;
                                    $objPartyContactMech->contact_mech_id = $modelObjContactMetch->contact_mech_id;
                                    $objPartyContactMech->role_type_id = "EMPLOYEE";
                                    $objPartyContactMech->from_date = now()->toDateString();
                                    $objPartyContactMech->save();
                                }

                                if(!isset($id_contact_mech_number)) {

                                    $objPartyContactMech = new PartyContactMech;
                                    $objPartyContactMech->party_id = $request->party_id;
                                    $objPartyContactMech->contact_mech_id = $seqNextContactMechTelecomNumber;
                                    $objPartyContactMech->role_type_id = "EMPLOYEE";
                                    $objPartyContactMech->from_date = now()->toDateString();
                                    $objPartyContactMech->save();
                                }

                                if(isset($id_contact_mech_address)){
                                    $objPostalAddres = PostalAddres::find($id_contact_mech_address);
                                }else{
                                    $seqNextContactMechPostalAddress = SequenceValueItem::where('seq_name','ContactMech')->select('seq_id')->first()->seq_id + 1;
                                    $objSeq = SequenceValueItem::where('seq_name','ContactMech');
                                    $objSeq->update(['seq_id'=>$seqNextContactMechPostalAddress]);
                                    $objPostalAddres = new PostalAddres;
                                    $objPostalAddres->contact_mech_id = $seqNextContactMechPostalAddress;
                                    $objContactMetch = new ContactMetch;
                                    $objContactMetch->contact_mech_id = $seqNextContactMechPostalAddress;
                                    $objContactMetch->contact_mech_type_id = "POSTAL_ADDRESS";
                                    $objContactMetch->save();
                                }

                                $objPostalAddres->address1              = $request->C_V;
                                $objPostalAddres->city                  = $request->ciudad;
                                $objPostalAddres->country_geo_id        = 'ECU';
                                $objPostalAddres->state_province_geo_id = $request->id_provincia;

                                if((!empty($request->nombre_contacto) &&  $request->nombre_contacto != null) &&
                                    (!empty($request->apellido_contacto) && $request->apellido_contacto != null) &&
                                    (!empty($request->telefono_contacto) && $request->telefono_contacto != null)) {

                                    $existPerson = Party::where('party_id',$request->party_id_contact)->first();
                                    if($existPerson != null){
                                        $objParty = Party::find($request->party_id_contact);
                                        $objPerson = Person::find($request->party_id_contact);
                                        $idContactMechContato = PartyContactMech::where('party_id', $request->party_id_contact)->select('contact_mech_id')->first();
                                        $objContactMetch = ContactMetch::find($idContactMechContato->contact_mech_id);
                                        $objTelecomNumber = TelecomNumber::find($idContactMechContato->contact_mech_id);
                                    }else{
                                        $seqNextPartyContacto = SequenceValueItem::where('seq_name','Party')
                                                ->select('seq_id')->first()->seq_id + 1;

                                        $objSequenceValueItem = SequenceValueItem::find('Party');
                                        $objSequenceValueItem->seq_id = $seqNextPartyContacto;
                                        $objSequenceValueItem->save();

                                        $objParty = new Party;
                                        $objParty->party_id = $seqNextPartyContacto;
                                        $objPerson = new Person;
                                        $objPerson->party_id = $seqNextPartyContacto;
                                        $objContactMechContato = new PartyContactMech;
                                        $objContactMetch = new ContactMetch;
                                        $objTelecomNumber = new TelecomNumber;
                                        $objPartyRole = new PartyRole;
                                    }

                                    $objParty->party_type_id = 'PERSON';

                                    if ($objParty->save()) {

                                        $objPerson->first_name = $request->nombre_contacto;
                                        $objPerson->last_name = $request->apellido_contacto;

                                        if ($objPerson->save()) {
                                            if($existPerson == null){
                                                $seqNextContactMech = SequenceValueItem::where('seq_name','ContactMech')
                                                        ->select('seq_id')->first()->seq_id + 1;

                                                $objSequenceValueItem = SequenceValueItem::find('ContactMech');
                                                $objSequenceValueItem->seq_id = $seqNextContactMech;
                                                $objSequenceValueItem->save();

                                                $objContactMetch->contact_mech_id = $seqNextContactMech;

                                                $objPartyRelationship = new PartyRelationship;
                                                $objPartyRelationship->party_id_from = $request->party_id;
                                                $objPartyRelationship->party_id_to = $seqNextPartyContacto;
                                                $objPartyRelationship->role_type_id_from = "EMPLOYEE";
                                                $objPartyRelationship->role_type_id_to = "CONTACTO_EMERGENCIA";
                                                $objPartyRelationship->party_relationship_type_id = "CONTACTO_EMER";
                                                $objPartyRelationship->from_date = now()->toDateString();
                                                $objPartyRelationship->save();

                                            }
                                            $objContactMetch->contact_mech_type_id = 'TELECOM_NUMBER';

                                            if ($objContactMetch->save()) {
                                                if($existPerson  == null)
                                                    $objTelecomNumber->contact_mech_id = $seqNextContactMech;

                                                $objTelecomNumber->country_code = '593';
                                                $objTelecomNumber->contact_number = $request->telefono_contacto;
                                                if($objTelecomNumber->save()){
                                                    if($existPerson == null) {
                                                        $objPartyRole->party_id = $seqNextPartyContacto;
                                                        $objPartyRole->role_type_id = "CONTACTO_EMERGENCIA";
                                                        $objPartyRole->status = true;
                                                        if($objPartyRole->save()){
                                                            $objContactMechContato->party_id = $seqNextPartyContacto;
                                                            $objContactMechContato->contact_mech_id = $seqNextContactMech;
                                                            $objContactMechContato->from_date = now()->toDateString();
                                                            $objContactMechContato->role_type_id = "CONTACTO_EMERGENCIA";
                                                            $objContactMechContato->save();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                if ($objPostalAddres->save()) {

                                    if(!isset($id_contact_mech_address)) {
                                        $objPartyContactMech = new PartyContactMech;
                                        $objPartyContactMech->party_id = $request->party_id;
                                        $objPartyContactMech->contact_mech_id = $seqNextContactMechPostalAddress;
                                        $objPartyContactMech->role_type_id = "EMPLOYEE";
                                        $objPartyContactMech->save();
                                    }

                                    $f = explode("-",$request->fecha_inicio);
                                    if ($request->has('activa')) {
                                        $datos = [
                                            ucwords($objConfigEmpresa[0]->nombre_empresa),
                                            $objConfigEmpresa[0]->ruc,
                                            $objConfigEmpresa[0]->direccion_empresa,
                                            ucwords($objConfigEmpresa[0]->representante),
                                            $objConfigEmpresa[0]->identificacion_representante,
                                            $request->ciudad . " " . $request->C_V,
                                            isset($request->id_cargo) ? Cargo::where('id_cargo', $request->id_cargo)->select('nombre')->first()->nombre : '',
                                            isset($request->salario) ? $request->salario : '',
                                            isset($request->horas) ? $request->horas : '',
                                            //isset($horasTrabajo) ? $horasTrabajo : '',
                                            $request->nacionalidad,
                                            //isset($request->letras) ? $request->letras : null,
                                            isset($f[2]) ? $f[2] : null,
                                            isset($f[1]) ? $f[1] : null,
                                            isset($f[0]) ? $f[0] : null,
                                            isset($request->cant_dias) ? $request->cant_dias : null,
                                            $request->correo,
                                            $request->funciones,
                                            $request->id_ciudad
                                        ];
                                    }

                                    $objContrataciones = Contrataciones::find($request->id_contratacion);
                                    $objContrataciones->id_tipo_contrato = $request->id_tipo_contrato;
                                    $objContrataciones->id_tipo_contrato_descripcion = tipoContratoDescripcion($request->id_tipo_contrato)->id_tipo_contrato_descripcion;
                                    $request->has('activa') ? $objContrataciones->estado = 1 : '';

                                    if ($objContrataciones->save()) {

                                        $objDetalleContratacion = DetalleContratacion::find($request->id_detalle_contrataciones);
                                        $objDetalleContratacion->id_cargo = $request->id_cargo;
                                        $objDetalleContratacion->fecha_expedicion_contrato = isset($request->fecha_inicio) ? $request->fecha_inicio : null;
                                        $objDetalleContratacion->horas_jornada_laboral = isset($request->horas) ? $request->horas : null;
                                        $objDetalleContratacion->nombre_archivo_contrato = $request->has('activa') ? makeContrato($request->nombres, $request->apellidos, $request->identificacion, $datos, $objContrato->cuerpo_contrato) : null;
                                        $objDetalleContratacion->salario = isset($request->salario) ? $request->salario : '';
                                        $objDetalleContratacion->tipo_documento = isset($request->tipo_documento) ? $request->tipo_documento : '';
                                        $objDetalleContratacion->decimo_tercero = isset($request->decimo_tercero) ? $request->decimo_tercero : null;
                                        $objDetalleContratacion->decimo_cuarto = isset($request->decimo_cuarto) ? $request->decimo_cuarto : null;
                                        $objDetalleContratacion->tipo_documento = isset($request->tipo_documento) ? $request->tipo_documento : '';
                                        $objDetalleContratacion->fondo_reserva = isset($request->fondo_reserva) ? $request->fondo_reserva : null;
                                        $objDetalleContratacion->duracion = isset($request->cant_dias) ? $request->cant_dias : null;
                                        $objDetalleContratacion->cantidad_letras = isset($request->letras) ? $request->letras : null;
                                        $objDetalleContratacion->retencion_iva = isset($request->retencion_iva) ? $request->retencion_iva : null;
                                        $objDetalleContratacion->id_ciudad = isset($request->id_ciudad) ? $request->id_ciudad : null;
                                        $objDetalleContratacion->funciones = isset($request->funciones) ? $request->funciones : null;
                                        $objDetalleContratacion->tipo_retencion_renta = isset($request->tipo_impuesto_renta) ? $request->tipo_impuesto_renta : null;
                                        $objDetalleContratacion->tipo_retencion_iva = isset($request->tipo_impuesto_iva) ? $request->tipo_impuesto_iva : null;

                                        $objDetalleContratacion->retencion_renta = isset($request->retencion_renta) ? $request->retencion_renta : null;
                                        $objDetalleContratacion->iva = isset($request->iva) ? $request->iva : null;

                                        if ($objDetalleContratacion->save()) {
                                            $objForeginContrataciones = ForeginContrataciones::find($request->id_contratacion);
                                            $objForeginContrataciones->id_tipo_contrato = $request->id_tipo_contrato;
                                            $objForeginContrataciones->id_tipo_contrato_descripcion = tipoContratoDescripcion($request->id_tipo_contrato)->id_tipo_contrato_descripcion;
                                            $request->has('activa') ? $objForeginContrataciones->estado = 1 : 0;

                                            if ($objForeginContrataciones->save()) {

                                                if(isset($request->tipo_impuesto_renta) && isset($request->tipo_impuesto_iva) && $request->tipo_documento == 'INVOICE_HONORARIOS'){

                                                    $retIva= explode('*',$request->tipo_impuesto_iva);
                                                    $retRenta= explode('*',$request->tipo_impuesto_renta);
                                                    $store = ProductStore::where('type_store','MATRIZ')->first();

                                                    PartyProfileDefault::updateOrCreate(
                                                        ['party_id' => $request->party_id],
                                                        [
                                                            'product_store_id' => $store->product_store_id ,
                                                            'party_id' => $request->party_id,
                                                            'default_pay_meth' => 'EFT_ACCOUNT',
                                                            'ret_ir_id' => $retRenta[1],
                                                            'ret_iva_id' => $retIva[1],
                                                            'last_updated_stamp' => now()->format('Y-m-d H:i:s'),
                                                            'last_updated_tx_stamp' => now()->format('Y-m-d H:i:s'),
                                                            'created_stamp' => now()->format('Y-m-d H:i:s'),
                                                            'created_tx_stamp' => now()->format('Y-m-d H:i:s')
                                                        ]
                                                    );

                                                }else{
                                                    PartyProfileDefault::where('party_id',$request->party_id)->delete();
                                                }

                                                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                                                            El contrato se ha guardado con exito!
                                                        </div>';
                                                $status = 1;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }else{

                if(getExistContrataciones($request->id_empleado)){
                    $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                                El empleado ya tiene ambos contratos activos actualmente (Contratación y Confidencialidad)
                                </div>';
                    $status = 0;

                }else{

                    $dataPerson = Person::where('person.party_id',$request->id_empleado)
                        ->join('party_identification as pi', 'person.party_id','=','pi.party_id')
                        ->join('party_contact_mech as pcm', 'person.party_id','=','pcm.party_id')
                        ->join('postal_address as pa','pcm.contact_mech_id','=','pa.contact_mech_id')
                        ->select('person.first_name','person.last_name','pi.id_value','pa.address1','pa.city')
                        ->first();
                    $f = explode("-",$request->fecha_inicio);

                    if($request->has('activa')) {
                        $datos = [
                            ucwords($objConfigEmpresa[0]->nombre_empresa),
                            $objConfigEmpresa[0]->ruc,
                            $objConfigEmpresa[0]->direccion_empresa,
                            ucwords($objConfigEmpresa[0]->representante),
                            $objConfigEmpresa[0]->identificacion_representante,
                            ucwords($dataPerson->city . " " . $dataPerson->address1),
                            isset($request->id_cargo) ? Cargo::where('id_cargo',$request->id_cargo)->select('nombre')->first()->nombre : '',
                            isset($request->salario) ? $request->salario : null,
                            isset($request->horas) ? $request->horas : null,
                            //isset($horasTrabajo) ? $horasTrabajo : null,
                            $request->nacionalidad,
                            //isset($request->letras) ? $request->letras : null,
                            isset($f[2]) ? $f[2] : null,
                            isset($f[1]) ? $f[1] : null,
                            isset($f[0]) ? $f[0] : null,
                            isset($request->cant_dias) ? $request->cant_dias : null,
                            $request->correo,
                            $request->funciones,
                            $request->id_ciudad
                        ];
                    }

                    $objContrataciones = Contrataciones::find($request->id_contratacion);
                    $objContrataciones->id_tipo_contrato             = $request->id_tipo_contrato;
                    $objContrataciones->id_tipo_contrato_descripcion = tipoContratoDescripcion($request->id_tipo_contrato)->id_tipo_contrato_descripcion;
                    $request->has('activa') ? $objContrataciones->estado = 1 : 0;

                    if($objContrataciones->save()) {
                        $objDetalleContratacion = DetalleContratacion::find($request->id_detalle_contrataciones);
                        $objDetalleContratacion->id_cargo                    = isset($request->id_cargo) ? $request->id_cargo : null;
                        $objDetalleContratacion->fecha_expedicion_contrato   = isset($request->fecha_inicio) ? $request->fecha_inicio : null;//isset($fechaInicioFormateada) ? $fechaInicioFormateada : null;
                        //$objDetalleContratacion->fecha_expiracion_contrato = isset($fechaFinFormateada) ? $fechaFinFormateada : null;
                        //$objDetalleContratacion->hora_entrada              = isset($dataHoraInicio) ? $dataHoraInicio : null;
                        //$objDetalleContratacion->hora_salida               = isset($dataHoraFin) ? $dataHoraFin : null;
                        $objDetalleContratacion->horas_jornada_laboral       = isset($request->horas) ? $request->horas : null; //isset($horasTrabajo) ? $horasTrabajo : null;
                        $objDetalleContratacion->nombre_archivo_contrato     = $request->has('activa') ? makeContrato($dataPerson->first_name,$dataPerson->last_name,$dataPerson->id_value,$datos,$objContrato->cuerpo_contrato) : null;
                        $objDetalleContratacion->tipo_documento              = isset($request->tipo_documento) ? $request->tipo_documento : '';
                        $objDetalleContratacion->salario                     = isset($request->salario) ? $request->salario : null;
                        $objDetalleContratacion->decimo_tercero              = isset($request->decimo_tercero) ? $request->decimo_tercero : null;
                        $objDetalleContratacion->decimo_cuarto               = isset($request->decimo_cuarto) ? $request->decimo_cuarto : null;
                        $objDetalleContratacion->fondo_reserva               = isset($request->fondo_reserva) ? $request->fondo_reserva : null;
                        $objDetalleContratacion->duracion                    = isset($request->cant_dias) ? $request->cant_dias : null;
                        $objDetalleContratacion->cantidad_letras             = isset($request->letras) ? $request->letras : null;
                        $objDetalleContratacion->id_ciudad                   = isset($request->id_ciudad) ? $request->id_ciudad : null;
                        $objDetalleContratacion->funciones                   = isset($request->funciones) ? $request->funciones : null;
                        $objDetalleContratacion->tipo_retencion_renta        = isset($request->tipo_impuesto_renta) ? $request->tipo_impuesto_renta : null;
                        $objDetalleContratacion->tipo_retencion_iva          = isset($request->tipo_impuesto_iva) ? $request->tipo_impuesto_iva : null;

                        if($objDetalleContratacion->save()){

                            $objForeginContrataciones = ForeginContrataciones::find($request->id_contratacion);
                            $objForeginContrataciones->id_tipo_contrato             = $request->id_tipo_contrato;
                            $objForeginContrataciones->id_tipo_contrato_descripcion = tipoContratoDescripcion($request->id_tipo_contrato)->id_tipo_contrato_descripcion;
                            $request->has('activa') ? $objForeginContrataciones->estado = 1 : '';

                            if ($objForeginContrataciones->save()) {

                                if(isset($request->tipo_impuesto_renta) && isset($request->tipo_impuesto_iva) && $request->tipo_documento == 'INVOICE_HONORARIOS'){

                                    $retIva= explode('*',$request->tipo_impuesto_iva);
                                    $retRenta= explode('*',$request->tipo_impuesto_renta);
                                    $store = ProductStore::where('type_store','MATRIZ')->first();

                                    PartyProfileDefault::updateOrCreate(
                                        ['party_id' => $request->id_empleado],
                                        [
                                            'product_store_id' => $store->product_store_id ,
                                            'party_id' => $request->id_empleado,
                                            'default_pay_meth' => 'EFT_ACCOUNT',
                                            'ret_ir_id' => $retRenta[1],
                                            'ret_iva_id' => $retIva[1],
                                            'last_updated_stamp' => now()->format('Y-m-d H:i:s'),
                                            'last_updated_tx_stamp' => now()->format('Y-m-d H:i:s'),
                                            'created_stamp' => now()->format('Y-m-d H:i:s'),
                                            'created_tx_stamp' => now()->format('Y-m-d H:i:s')
                                        ]
                                    );

                                }

                                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                                            El contrato se ha guardado con exito!
                                        </div>';
                                $status = 1;
                            }
                        }
                    }
                }
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
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
            $status = 0;
        }

        return response()->json(['status'=>$status,'msg'=>$msg]);
    }

    public function letras(Request $request){
        return valorEnLetras($request->cadena);
    }

    public function add_addendum(Request $request){

        $contratacion = Contrataciones::where([
            ['contrataciones.id_contrataciones',$request->id_contratacion],
            //['contrataciones.estado',1],
            //['contrataciones.id_tipo_contrato_descripcion',2]
        ])->join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')
            ->join('tipo_contrato as tc','contrataciones.id_tipo_contrato','tc.id_tipo_contrato')
            ->join('cargos as c','dc.id_cargo','c.id_cargo')
            ->select(
                'contrataciones.id_empleado',
                'horas_jornada_laboral',
                'salario',
                'dc.id_cargo',
                'c.nombre as nombre_cargo',
                'id_detalle_contrataciones',
                'dc.fecha_expedicion_contrato',
                'tc.relacion_dependencia',
                'tc.nombre as nombre_contrato',
                'iva','retencion_iva',
                'retencion_renta',
                'contrataciones.id_contrataciones',
                'tc.relacion_dependencia',
                'dc.decimo_tercero',
                'dc.decimo_cuarto',
                'dc.fondo_reserva',
                'dc.iva',
                'dc.retencion_iva',
                'dc.retencion_renta',
                'dc.tipo_documento',
                'dc.tipo_retencion_iva',
                'dc.tipo_retencion_renta'
            )->first();

        return view('layouts.views.contrataciones.partials.form_addemdum',[
            'dataCargos' => Cargo::all(),
            'dataContratacion' => $contratacion,
            'dataAddendum' => Addendum::where('id_contratacion',$request->id_contratacion)->get(),
            'bancos' => Enumeration::where('enum_type_id','TIPO_BANCO')->orderBy('description', 'asc')->get(),
            'datosBancarios' => PaymentMethod::join('eft_account as ea','payment_method.payment_method_id','ea.payment_method_id')
                                ->where('payment_method.party_id',$contratacion->id_empleado)
                                ->whereNull('thru_date')
                                ->select('ea.payment_method_id','ea.account_type','ea.codigo_banco','ea.account_number')->first(),
            'iva' => Iva::all(),
            'tipoImpuestos' => DeductionType::where('activo','S')->get(),
            'tipoDocumentos'=> tipoDocumentosNomina()
        ]);
    }

    public function storeAddendumContrataciones(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'salario'                 => 'required',
            'horas'                   => 'required',
            'id_cargo'                => 'required',
            'letras'                  => 'required',
            'cuerpo_addendum'         => 'required',
            'id_detalle_contratacion' => 'required',
            //'iva                    ' => 'required',
            //'retencion_iva'           => 'required',
            //'retencion_renta'         => 'required',
        ]);

        if (!$valida->fails()) {

            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                        Hubo un inconveniente al tratar de guardar el addendum, intente de nuevo!
                   </div>';
            $status = 0;

            //dd($request->iva,$request->retencion_iva,$request->retencion_renta);
            $objDetallesContraraciones = DetalleContratacion::find($request->id_detalle_contratacion);
            $objDetallesContraraciones->salario = $request->salario;
            $objDetallesContraraciones->horas_jornada_laboral = $request->horas;
            $objDetallesContraraciones->id_cargo = $request->id_cargo;
            $objDetallesContraraciones->cantidad_letras = $request->letras;
            $request->iva != "undefined" ? $objDetallesContraraciones->iva = $request->iva : "";
            $request->retencion_iva != "undefined" ? $objDetallesContraraciones->retencion_iva = $request->retencion_iva : "";
            $request->retencion_renta != "undefined" ? $objDetallesContraraciones->retencion_renta = $request->retencion_renta : "";

            if($objDetallesContraraciones->save()){
                $dataDetalleContratacion = DetalleContratacion::where('id_detalle_contrataciones',$request->id_detalle_contratacion)->select('id_contrataciones')->first();

                $objAddendum = new Addendum;
                $objAddendum->id_contratacion = $dataDetalleContratacion->id_contrataciones;
                $objAddendum->cuerpo_addendum = $request->cuerpo_addendum;
                $objAddendum->fecha           = Carbon::now()->toDateString();

                $objContrataciones= Contrataciones::where('id_contrataciones',$dataDetalleContratacion->id_contrataciones)->select('id_empleado')->first();
                $objConfigEmpresa = ConfiguracionEmpresa::select('nombre_empresa','ruc','direccion_empresa','representante')->first();
                $dataPerson       = Person::where('person.party_id',$objContrataciones->id_empleado)->join('party_identification as pi','person.party_id','pi.party_id')
                                    //->join('party_contact_mech as pcm','pi.party_id','pcm.party_id')
                                    //->join('postal_address as pa','pcm.contact_mech_id','pa.contact_mech_id')
                                    ->select('first_name','last_name','id_value',/*'address1','city',*/'nacionalidad')->first();

                $postalAddress = getPostalAddres($objContrataciones->id_empleado);
                $f = explode("-",$request->fecha_inicio);
                $datos = [
                    ucwords($objConfigEmpresa->nombre_empresa),
                    $objConfigEmpresa->ruc,
                    $objConfigEmpresa->direccion_empresa,
                    ucwords($objConfigEmpresa->representante),
                    $objConfigEmpresa->identificacion_representante,
                    $postalAddress->city . " " . $postalAddress->address1,
                    Cargo::where('id_cargo',$request->id_cargo)->select('nombre')->first()->nombre,
                    $request->salario,
                    $request->horas,
                    $dataPerson->nacionalidad,
                    isset($f[2]) ? $f[2] : null,
                    isset($f[1]) ? $f[1] : null,
                    isset($f[0]) ? $f[0] : null,
                    isset($request->cant_dias) ? $request->cant_dias : null
                ];
                $objAddendum->nombre_archivo = makeContrato($dataPerson->first_name,$dataPerson->last_name, $dataPerson->id_value,$datos,$request->cuerpo_addendum);

                if($objAddendum->save()){

                    $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                              El addendum se ha guardado con exito!
                            </div>';
                    $status = 1;
                }
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
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
            $status = 0;
        }

        return response()->json(array('status'=>$status,'msg'=>$msg));

    }

    public function validaSueldoSectorial(Request $request){

        $sueldo_minimo_sectorial = Cargo::where('id_cargo',$request->id_cargo)->select('sueldo_minimo_sectorial')->first()->sueldo_minimo_sectorial;
        $tipo_contrato = TipoContratos::where('id_tipo_contrato',$request->id_tipo_contrato)->first()->sueldo_sectorial;

        return response()->json(
            [
                'sueldo_minimo_sectorial'=>$sueldo_minimo_sectorial,
                'tipo_contrato'=>$tipo_contrato
            ]);

    }

    public function formBonosFijos(Request $request){

        $dataBono = BonoFijo::where('id_contratacion',$request->id_contratacion);
        $dataPrestamo = Prestamo::where([
            ['id_contratacion',$request->id_contratacion],
            ['pagado',0]
        ]);

        $contratacion = ForeginContrataciones::find($request->id_contratacion);

        return view('layouts.views.contrataciones.partials.form_bono_fijo',[
            'idBonoFijo' => $request->id_bono_fijo,
            'idContratacion' => $request->id_contratacion,
            'dataBono' => $dataBono,
            'dataPrestamo' => $dataPrestamo,
            'persona' => $contratacion->person->first_name.' '.$contratacion->person->last_name
        ]);
    }

    public function storeBonosFijos(Request $request){

        $valida = Validator::make($request->all(), [
            'arrData' => 'required|Array',
        ]);

        if (!$valida->fails()) {

            BonoFijo::where('id_contratacion',$request->arrData[0][2])->delete();

            foreach ($request->arrData as $data) {

                $objPrestamo = new BonoFijo;
                $objPrestamo->id_contratacion = $data[3];
                $objPrestamo->nombre = $data[0];
                $objPrestamo->monto = $data[1];
                $objPrestamo->apt_personal = $data[5];
                $objPrestamo->fecha_asignacion = Carbon::parse($data[4])->format('Y-m-05');

                if ($objPrestamo->save()) {
                    $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                             El bono fijo se ha guardado con exito!
                        </div>';
                    $status = 1;
                } else {
                    $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                             Ha ocurrido un inconveniente al tratar de guardar el bono fijo, intente nuevamente !
                        </div>';
                    $status = 0;
                }
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
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
            $status = 0;
        }
        return response()->json(['status'=>$status,'msg'=>$msg]);
    }

    public function inputsBonosFijos(Request $request){

       // BonoFijo::where('id_contratacion',);
        return view('layouts.views.contrataciones.partials.inptus_bono_fijo',[
            'cant' => $request->cant_inputs,

        ]);
    }

    public function inputsPrestamos(Request $request){

        return view('layouts.views.contrataciones.partials.inputs_prestamo',[
            'cant' => $request->cant_inputs,

        ]);
    }

    public function storePrestamo(Request $request){

       //dd($request->all());
        $valida = Validator::make($request->all(), [
            //'arrData' => 'required|Array',
        ]);

        if (!$valida->fails()) {

            foreach ($request->arrData as $data) {

                $contratacion = Contrataciones::join('detalles_contrataciones as dc','contrataciones.id_contrataciones','dc.id_contrataciones')
                                ->join('tipo_contrato as tc', 'contrataciones.id_tipo_contrato','tc.id_tipo_contrato')
                                ->where([
                                    ['contrataciones.id_contrataciones', $data[3]],
                                    ['contrataciones.id_tipo_contrato_descripcion',2],
                                    ['contrataciones.estado',1],
                                ])->select('relacion_dependencia')->first();

                $objPrestamo = ($data[2] != null && $data[2] !='') ? Prestamo::find($data[2]) : new Prestamo;
                $objPrestamo->id_contratacion = $data[3];
                $objPrestamo->nombre = $data[0];
                $objPrestamo->cuota = $data[1];
                $objPrestamo->total = $data[4];
                $objPrestamo->fecha_inicio_descuento = Carbon::parse($data[5])->format('Y-m-05');
                $objPrestamo->persona = $data[6];
                $objPrestamo->invoice_item_type_id = $contratacion->relacion_dependencia ? 'PRESTAMOS_PR' : 'PRESTAMOS_PURCHASE';

                if ($objPrestamo->save()) {
                    $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                             El prestamo se ha guardado con exito!
                        </div>';
                    $status = 1;
                } else {
                    $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                             Ha ocurrido un inconveniente al tratar de guardar el prestamo, intente nuevamente !
                        </div>';
                    $status = 0;
                }

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
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
            $status = 0;
        }
        return response()->json(['status'=>$status,'msg'=>$msg]);
    }

    public function deleteBonosFijos(Request $request){

        if(BonoFijo::destroy($request->id_bono_fijo)){
            $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                             El bono fijo se ha eliminado con exito!
                        </div>';
            $status = 1;
        } else {
            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                             Ha ocurrido un inconveniente al tratar de eliminar el bono fijo, intente nuevamente !
                        </div>';
            $status = 0;
        }
        return response()->json(['status'=>$status,'msg'=>$msg]);
    }

    public function updateDetalleContratacion(Request $request){

        /*$valida = Validator::make($request->all(), [
            'iva'                 => 'required',
            'retencion_iva'                   => 'required',
            'retencion_renta'                => 'required',
        ]);*/

        //if (!$valida->fails()) {

            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                        Hubo un inconveniente al tratar de actualizar los datos, intente de nuevo!
                   </div>';
            $status = 0;

            $objDetallesContraraciones = DetalleContratacion::find($request->id_detalle_contratacion);
            isset($request->retencion_renta) ? $objDetallesContraraciones->retencion_renta = $request->retencion_renta : "";
            isset($request->retencion_iva)   ? $objDetallesContraraciones->retencion_iva = $request->retencion_iva : "";
            isset($request->iva)             ? $objDetallesContraraciones->iva = $request->iva : "";
            isset($request->decimo_tercero)  ? $objDetallesContraraciones->decimo_tercero = $request->decimo_tercero : "";
            isset($request->decimo_cuarto)   ? $objDetallesContraraciones->decimo_cuarto = $request->decimo_cuarto : "";
            isset($request->fondo_reserva)   ? $objDetallesContraraciones->fondo_reserva = $request->fondo_reserva : "";
            isset($request->horas_laborales)   ? $objDetallesContraraciones->horas_jornada_laboral = $request->horas_laborales : "";
            isset($request->salario) ? $objDetallesContraraciones->salario = $request->salario : "";
            isset($request->salario) ? $objDetallesContraraciones->cantidad_letras = trim(valorEnLetras($request->salario)) : "";
            isset($request->tipo_documento) ? $objDetallesContraraciones->tipo_documento = $request->tipo_documento : "";
            $objDetallesContraraciones->tipo_retencion_renta = isset($request->tipo_retencion_renta) ? $request->tipo_retencion_renta : null;
            $objDetallesContraraciones->tipo_retencion_iva = isset($request->tipo_retencion_iva) ? $request->tipo_retencion_iva : null;

            if($objDetallesContraraciones->save()){

                $person = Person::where('party_id',$request->party_id)->first();

                if(isset($request->payment_method_id)){

                    $objPaymentMethod = PaymentMethod::find($request->payment_method_id);

                }else{

                    $objPaymentMethod =  new PaymentMethod;
                    $seqNextPaymentMethod = SequenceValueItem::where('seq_name','PaymentMethod')->select('seq_id')->first()->seq_id+1;

                    $objPaymentMethod->payment_method_id = $seqNextPaymentMethod;
                    $objPaymentMethod->payment_method_type_id = 'EFT_ACCOUNT'; //TRANSFERENCIA BANCARIA
                    $objPaymentMethod->party_id= $request->party_id;
                    $objPaymentMethod->description= $person->first_name.' '.$person->last_name;
                    $objPaymentMethod->from_date=now()->toDateTimeString();
                    $objPaymentMethod->created_stamp=now()->toDateTimeString();
                    $objPaymentMethod->created_tx_stamp=now()->toDateTimeString();
                    $objPaymentMethod->save();

                }

                $existEftaccount = EftAccount::where('payment_method_id',$objPaymentMethod->payment_method_id)->first();

                $objeftAcount = isset($existEftaccount) ? EftAccount::find($objPaymentMethod->payment_method_id) : new EftAccount;
                $objeftAcount->payment_method_id= $objPaymentMethod->payment_method_id;
                $objeftAcount->account_type= $request->tipo_cuenta;
                $objeftAcount->codigo_banco = $request->id_banco;
                $objeftAcount->account_number = $request->numero_cuenta;
                $objeftAcount->name_on_account = $person->first_name.' '.$person->last_name;
                $objeftAcount->last_updated_stamp= now()->toDateTimeString();
                $objeftAcount->last_updated_tx_stamp= now()->toDateTimeString();

                if($objeftAcount->save()){

                    if(!isset($request->payment_method_id)){

                        $objSequenceValueItem= SequenceValueItem::where('seq_name','PaymentMethod');
                        $objSequenceValueItem->update(['seq_id' => $seqNextPaymentMethod+1]);

                    }

                    if(isset($request->tipo_retencion_renta) && isset($request->tipo_retencion_iva) && $request->tipo_documento == 'INVOICE_HONORARIOS'){

                        $retIva= explode('*',$request->tipo_retencion_iva);
                        $retRenta= explode('*',$request->tipo_retencion_renta);
                        $store = ProductStore::where('type_store','MATRIZ')->first();

                        PartyProfileDefault::updateOrCreate(
                            ['party_id' => $request->party_id],
                            [
                                'product_store_id' => $store->product_store_id ,
                                'party_id' => $request->party_id,
                                'default_pay_meth' => 'EFT_ACCOUNT',
                                'ret_ir_id' => $retRenta[1],
                                'ret_iva_id' => $retIva[1],
                                'last_updated_stamp' => now()->format('Y-m-d H:i:s'),
                                'last_updated_tx_stamp' => now()->format('Y-m-d H:i:s'),
                                'created_stamp' => now()->format('Y-m-d H:i:s'),
                                'created_tx_stamp' => now()->format('Y-m-d H:i:s')
                            ]
                        );

                    }else{
                        PartyProfileDefault::where('party_id',$request->party_id)->delete();
                    }

                    $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                                El contrato se ha guardado con éxito!
                            </div>';
                            $status = 1;
                }

                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                          Se ha actualizado los datos de la contratación exitosamente!
                        </div>';
                $status = 1;
            }
        /*}else {
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
            $status = 0;
        }*/

        return response()->json(array('status'=>$status,'msg'=>$msg));
    }

    public function deletePrestamo(Request $request){

        $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                    Ha ocurrido un inconveniente al tratar de eliminar el prestamo, intente nuevamente !
                </div>';
        $status = 0;

        if(Prestamo::destroy($request->id_prestamo)){
            $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                    El prestamo se ha eliminado con éxito!
                </div>';
            $status = 1;
        }

        return response()->json(array('success'=>$status,'msg'=>$msg));
    }

    public function formCashManagementPrestamo()
    {
        return view('layouts.views.contrataciones.partials.form_cash_management',[
            'prestamos' => Prestamo::whereNotIn('id_prestamo',function($query){
                                $query->select('id_registro')->from('referencia_pago')->where('tipo','prestamo');
                            })->join('contrataciones as c','prestamo.id_contratacion','c.id_contrataciones','c.id_empleado')
                            ->where([
                                ['pagado',false],
                                ['c.estado',true]
                            ])->select('prestamo.*')->get()
        ]);
    }

    public function downloadCashManagementPrestamo(Request $request)
    {
        $cuentaEmpresa = cuentaEmpresa();

        $prestamos = Prestamo::where([
            ['pagado',false],
            ['c.estado',1]
        ])->whereNotIn('id_prestamo',function($query){
            $query->select('id_registro')->from('referencia_pago')->where('tipo','prestamo');
        })->join('contrataciones as c','prestamo.id_contratacion','c.id_contrataciones')
        ->whereIn('id_contratacion',$request->contrataciones)->select('prestamo.*','c.id_empleado')->get();

        $dataFile='';

        foreach($prestamos as $prestamo){

            $dataContratacion = contratacionesCashManagement($prestamo->id_empleado)->first();

            $dataFile.= "PA\t".str_pad($cuentaEmpresa->account_number,20)."\t1\t1\t".str_pad($dataContratacion->id_value,13)."\tUSD\t".str_pad(str_replace(['.',','],'',number_format($prestamo->total,2)),13,'0',STR_PAD_LEFT)."\tCTA\t". str_pad($dataContratacion->enum_code2,13)."\t".$dataContratacion->tipo_cuenta."\t".str_pad($dataContratacion->account_number,20)."\t".$dataContratacion->codigo_identificacion."\t".str_pad($dataContratacion->id_value,14)."\t".str_pad("PAGO PRESTAMO ". $dataContratacion->empleado,40)."\t\n";

        }

        return base64_encode($dataFile);
    }

    public function storeReferenciaBancariaPrestamos(Request $request)
    {
        $valida =  Validator::make($request->all(), [
            'referencia'   => 'required',
            'contrataciones' => 'required'
        ],[
            'referencia.required' => 'La referencia es obligatoria',
            'contrataciones.required' => 'Debe seleccionar al menos a un empleado para realizar el pago'
        ]);

        $msg = '';
        $status = false;

        if(!$valida->fails()) {

            $conexion = getConnection(0);

            DB::connection($conexion)->beginTransaction();
            DB::beginTransaction();

            try{

                $prestamos = Prestamo::join('contrataciones as c','prestamo.id_contratacion','c.id_contrataciones')
                            ->whereNotIn('id_prestamo',function($query){
                                $query->select('id_registro')->from('referencia_pago')->where('tipo','prestamo');
                            })->whereIn('id_contratacion',array_column($request->contrataciones,'id_contratacion'))
                            ->where([
                                ['c.estado',1],
                                ['pagado',false]
                            ])->select('prestamo.*','c.id_empleado')->get();

                $empresa = cuentaEmpresa();

                $glAccountDebito = glAccountMapPayment('EMPLOYEE_LOAN');

                if(!isset($glAccountDebito)){
                    $msgPersonal=true;
                    throw new Exception('No existe una cuenta contable configurada para el tipo de pago EMPLOYEE_LOAN');
                }

                foreach($prestamos as $prestamo){

                    $existePago = ReferenciaPago::where([
                        'referencia' => $request->referencia,
                        'id_registro' => $prestamo->id_prestamo,
                        'tipo' => 'prestamo'
                    ])->exists();

                    $person = getPerson($prestamo->id_empleado);

                    if($existePago){
                        $msgPersonal=true;
                        throw new Exception('<br />Ya existe el pago para el prestamo del empleado '.$person->first_name .' '.$person->last_name.' por el monto '.$prestamo->total);
                    }

                    $seqFinAccountTrans = SequenceValueItem::where('seq_name','FinAccountTrans')->first();
                    $finAccountTransId = $seqFinAccountTrans->seq_id+1;

                    $finAccountTrans = new FinAccountTrans;
                    $finAccountTrans->fin_account_trans_id = $finAccountTransId;
                    $finAccountTrans->fin_account_trans_type_id = 'WITHDRAWAL';
                    $finAccountTrans->fin_account_id = $empresa->fin_account_id;
                    $finAccountTrans->party_id = $prestamo->id_empleado;
                    $finAccountTrans->transaction_date = now()->toDateTimeString();
                    $finAccountTrans->entry_date = now()->toDateTimeString();
                    $finAccountTrans->amount = $prestamo->total;
                    $finAccountTrans->performed_by_party_id = session('dataUsuario')['id_empleado'];
                    $finAccountTrans->comments = 'Prestamo a empresarial a '.$person->first_name .' '.$person->last_name;
                    $finAccountTrans->status_id = 'FINACT_TRNS_CREATED';
                    $finAccountTrans->created_stamp = now()->toDateTimeString();
                    $finAccountTrans->created_tx_stamp = now()->toDateTimeString();
                    $finAccountTrans->last_updated_stamp = now()->toDateTimeString();
                    $finAccountTrans->last_updated_tx_stamp = now()->toDateTimeString();

                    if($finAccountTrans->save()){

                        $referenciaPago = new ReferenciaPago;
                        $referenciaPago->tipo = 'prestamo';
                        $referenciaPago->id_registro = $prestamo->id_prestamo;
                        $referenciaPago->referencia = $request->referencia;
                        $referenciaPago->payment_id = 0;
                        $referenciaPago->fecha = now()->toDateString();
                        $referenciaPago->save();

                    }

                    $dataAcctg =[
                        'acctg_trans_type_id' => 'OUTGOING_PAYMENT',
                        'gl_fiscal_type_id' => 'ACTUAL',
                        'is_posted' => 'Y',
                        'party_id' => $prestamo->id_empleado,
                        'fin_account_trans_id' => $finAccountTransId,
                        'description' => 'Prestamo empresarial a '. $person->first_name .' '.$person->last_name,
                        'created_by_user_login' => session('dataUsuario')['id_usuario_log'],
                        'last_modified_by_user_login' => session('dataUsuario')['id_usuario_log'],
                        'role_type_id' => 'BILL_FROM_VENDOR'
                    ];

                    //PRESTAMOS EMPLEADOS
                    $dataAcctg['debitos'][]= [
                        'organization_party_id' => $empresa->party_id,
                        'gl_account_id' => $glAccountDebito->gl_account_id,
                        'amount' => $prestamo->total
                    ];

                    //BANCOS
                    $dataAcctg['creditos'][] =[
                        'organization_party_id' => $empresa->party_id,
                        'gl_account_id' => $empresa->post_to_gl_account_id,
                        'amount' => $prestamo->total
                    ];

                    $res = crearAcctgTrans($dataAcctg);

                    if(!$res['success']){
                        $msgPersonal=true;
                        throw new \Exception('No se pudo crear el asiento contable de la transacción del libro banco '. $res['msg']);
                    }

                    SequenceValueItem::where('seq_name','FinAccountTrans')->update(['seq_id' => $finAccountTransId]);

                }

                DB::commit();
                DB::connection($conexion)->commit();
                $status = true;
                $msg .= '<div class="alert alert-success" role="alert" style="margin-bottom: 10px">
                              Se ha generado el pagos de los prestamos
                          </div>';

            }catch(\Exception $e){
                $status = false;
                $msg .= '<div class="alert alert-danger" role="alert" style="margin-bottom: 10px">
                                No pudo ser generado el pagos de los prestamos '
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
