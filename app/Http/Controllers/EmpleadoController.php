<?php

namespace App\Http\Controllers;

use App\Models\Contrataciones;
use App\Models\ForeginContrataciones;
use App\Models\Person;
use Illuminate\Http\Request;
use App\Models\PartyRole;
use App\Models\PartyIdentification;
use App\Models\PartyContactMech;
use App\Models\ContactMetch;
use App\Models\TelecomNumber;
use App\Models\PostalAddres;
use App\Models\PartyRelationShip;
use App\Models\Party;
use App\Models\PaymentMethod;
use DB;
use Validator;
use App\Models\SequenceValueItem;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        return view('layouts.views.empleados.list',
            [
                /*'dataEmpleados' => PartyRole::where('party_role.party_id',function ($query) use ($request){
                        if(!isset($request->estado) || $request->estado==1){
                            $query->select('party_id')->from('contrataciones as con')->where('con.estado',1);
                            $request->estado=1;
                        }else{
                            $query->select('party_id')->from('contrataciones as con')->whereIn('con.estado',[2,3]);
                        }
                    })->join('person as p', 'party_role.party_id','=','p.party_id')
                    ->join('party_identification as pi','p.party_id','=','pi.party_id')
                    ->where([
                        //['party_role.role_type_id','EMPLOYEE'],
                       ['party_role.status',1]
                    ])->select('id_value','first_name','last_name','status','party_role.party_id')->distinct()->paginate(10),*/
                'dataEmpleados' => PartyRole::join('person as p', 'party_role.party_id','=','p.party_id')
                    ->join('party_identification as pi','p.party_id','=','pi.party_id')
                    ->where([
                        ['party_role.status',isset($request->estado) ? $request->estado : 1],
                        ['party_role.role_type_id','EMPLOYEE']
                    ])->select('id_value','first_name','last_name','status','party_role.party_id')->paginate(10),
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit(Request $request)
    {
        $existEmpleadoActivo = Contrataciones::where([
            ['id_empleado',$request->id_empleado],
            ['estado',1],
            ['id_tipo_contrato_descripcion',2]
        ])->count();

        if($existEmpleadoActivo > 0){
            $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                     El empleado no puede ser anulado ya que tiene un contrato vigente aún.!
               </div>';
            $status = 0;
        }else{
            $objPartyRole = PartyRole::find($request->id_empleado);
            $objPartyRole->status = $request->estado == 1 ? 0 : 1;
            if($objPartyRole->save()){
                $request->estado == 1 ? $accion = ' desactivado ' : $accion = ' activado ';
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                     Se ha '.$accion.' el empleado exitosamente
               </div>';
                $status = 1;
            }
        }
        return response()->json(['status'=>$status,'msg'=>$msg]);
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


    public function updateDataEmpleado(Request $request){
        $valida =  Validator::make($request->all(), [

            'nombres'            => 'required',
            'apellidos'          => 'required',
            'nacimiento'         => 'required',
            'genero'             => 'required',
            'tipo_identificacion'=> 'required',
            'identificacion'     => 'required',
            'correo'             => 'required|email',
            'telefono'           => 'required',
            'ciudad'             => 'required',
            'provincia'          => 'required',
            //'nombre_contacto'    => 'required',
            //'apellido_contacto'  => 'required',
            //'telefono_contacto'  => 'required',
            'C_V'                => 'required',
        ]);

        $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                     ha ocurrido un error al actualizar los datos del empleado, intente nuevamente!
                </div>';
        $status = 0;

        if(!$valida->fails()) {

            $objPerson = Person::find($request->party_id);
            $objPerson->first_name = $request->nombres;
            $objPerson->last_name  = $request->apellidos;
            $objPerson->gender     = $request->genero;
            $objPerson->birth_date = $request->nacimiento;

            if($objPerson->save()){

                $objPartyIdentification = PartyIdentification::find($request->party_id);
                $objPartyIdentification->party_identification_type_id = $request->tipo_identificacion;
                $objPartyIdentification->id_value                     = $request->identificacion;

                if($objPartyIdentification->save()){

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
                    //dd($id_contact_mech_email,$id_contact_mech_number,$id_contact_mech_address);
                    $objPartyContactMech = PartyContactMech::where('party_id',$request->party_id)->first();

                    if(isset($id_contact_mech_email)){
                        $objContactMetch = ContactMetch::find($id_contact_mech_email);
                        $objContactMetch->update(['info_string' => $request->correo]);
                    }else{
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
                                $objPartyContactMech->party_id        = $request->party_id;
                                $objPartyContactMech->contact_mech_id = $seqNextContactMech;
                                $objPartyContactMech->role_type_id    = 'EMPLOYEE';
                                $objPartyContactMech->from_date       = date('Y/m/d');}
                                $objPartyContactMech->save();
                        }
                    }

                    if(isset($id_contact_mech_number)){
                        $objTelecomNumber = TelecomNumber::find($id_contact_mech_number);
                        $objTelecomNumber->update(['contact_number' => $request->telefono]);

                    }else{

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
                                    $objPartyContactMech->party_id = $request->party_id;
                                    $objPartyContactMech->contact_mech_id = $seqNextContactMech;
                                    $objPartyContactMech->role_type_id = 'EMPLOYEE';
                                    $objPartyContactMech->from_date = date('Y/m/d');
                                    $objPartyContactMech->save();
                                }
                            }
                        }
                    }

                    if(isset($id_contact_mech_address)){

                        $objPostalAddres = PostalAddres::find($id_contact_mech_address);
                        $objPostalAddres->address1              = $request->C_V;
                        $objPostalAddres->city                  = $request->ciudad;
                        $objPostalAddres->state_province_geo_id = $request->provincia;
                        $objPostalAddres->save();

                    }else{

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
                                    $objPartyContactMech->party_id        = $request->party_id;
                                    $objPartyContactMech->contact_mech_id = $seqNextContactMech;
                                    $objPartyContactMech->role_type_id    = 'EMPLOYEE';
                                    $objPartyContactMech->from_date       = date('Y/m/d');
                                    $objPartyContactMech->save();
                                }
                            }
                        }
                    }

                    if($request->nombre_contacto != null && $request->apellido_contacto != null && $request->telefono_contacto != null){

                        $objPartyRelationShip = PartyRelationShip::where([
                            ['party_id_from',$request->party_id],
                            ['role_type_id_to','CONTACT']
                        ])->join('person as p','party_relationship.party_id_to','p.party_id')
                        ->select('party_id_to')->first();


                        if($objPartyRelationShip == null){

                            $seqNextPartyContacto = SequenceValueItem::where('seq_name','Party')
                                    ->select('seq_id')->first()->seq_id + 1;

                            $objSequenceValueItem = SequenceValueItem::find('Party');
                            $objSequenceValueItem->seq_id = $seqNextPartyContacto;

                            if($objSequenceValueItem->save()){
                                $objPartyContacto = new Party;
                                $objPartyContacto->party_id      = $seqNextPartyContacto;
                                $objPartyContacto->party_type_id = 'PERSON';

                                if($objPartyContacto->save()) {
                                    $objPersonContacto = new Person;
                                    $objPersonContacto->party_id = $seqNextPartyContacto;
                                    $objPersonContacto->first_name = $request->nombre_contacto;
                                    $objPersonContacto->last_name = $request->apellido_contacto;

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
                                                            $objPartyRelationShipContacto->party_id_from     = $request->party_id;
                                                            $objPartyRelationShipContacto->party_id_to       = $seqNextPartyContacto;
                                                            $objPartyRelationShipContacto->role_type_id_from = 'EMPLOYEE';
                                                            $objPartyRelationShipContacto->role_type_id_to   = 'CONTACTO_EMERGENCIA';
                                                            $objPartyRelationShipContacto->party_relationship_type_id ='CONTACTO_EMER';
                                                            $objPartyRelationShipContacto->from_date         = now()->toDateString();
                                                            $objPartyRelationShipContacto->save();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                        }else{

                            $objPerson = Person::find($objPartyRelationShip->party_id_to);
                            $objPerson->first_name = $request->nombre_contacto;
                            $objPerson->last_name  = $request->apellido_contacto;

                            if($objPerson->save()){
                                $objPartyContactMech = PartyContactMech::where([
                                    ['party_contact_mech.party_id',$objPartyRelationShip->party_id_to],
                                    ['cm.contact_mech_type_id','TELECOM_NUMBER']
                                ])->join('contact_mech as cm','party_contact_mech.contact_mech_id','cm.contact_mech_id')
                                ->first();

                                if(isset($objPartyContactMech)){
                                    $objTelecomNumber = TelecomNumber::find($objPartyContactMech->contact_mech_id);
                                    $objTelecomNumber->contact_number = $request->telefono_contacto;
                                    $objTelecomNumber->save();
                                }

                            }
                        }

                    }
                    $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                               Los datos del empleado se han actualizado con exito!
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

        return response()->json(['msg'=>$msg,'status'=>$status]);
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

    public function datosFaltantes(Request $request){

        $email = '<div class="col-md-4 emial">
                    <div class="form-group">
                        <div class="input-group">
                           <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> E-mail </span>
                              <input type="email" class="form-control" id="correo" name="correo" value="" minlength="11" required="">
                        </div>
                    </div>
                 </div>
                    </div>';
        $tlf =  '<div class="col-md-4 tlf">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">  Teléfono </span>
                                <input type="text" class="form-control" id="telefono" value="" name="telefono" minlength="7" required>
                            </div>
                        </div>
                    </div>';
        $nacionalidad = '<div class="col-md-4 nacionalidad">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">  Nacionalidad </span>
                                <input type="text" class="form-control" id="nacionalidad" name="nacionalidad" value="" minlength="2" required>
                            </div>
                        </div>
                    </div>';
        $provincia= '<div class="col-md-4 provincia">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> Provincia </span>
                                <select class="form-control" id="id_provincia" name="id_provincia" required>
                                    <option disabled="" selected="">Seleccione</option>
                                    <option value="EC-PC">Pichincha</option>
                                    <option value="EC-GY">Guayas</option>
                                    <option value="EC-SU">Sucumbios</option>
                                    <option value="EC-NA">Napo</option>
                                    <option value="EC-PA">Pastaza</option>
                                    <option value="EC-SD">Santo Dominco de los Tsachilas</option>
                                    <option value="EC-AZ">Azuay</option>
                                    <option value="EC-BO">Bolivar</option>
                                    <option value="EC-CN">Cañar</option>
                                    <option value="EC-CR">Carchi</option>
                                    <option value="EC-CB">Chimborazo</option>
                                    <option value="EC-CT">Cotopaxi</option>
                                    <option value="EC-EO">El Oro</option>
                                    <option value="EC-ES">Exmeraldas</option>
                                    <option value="EC-GA">Galápagos</option>
                                    <option value="EC-IM">Imbabura</option>
                                    <option value="EC-LJ">Loja</option>
                                    <option value="EC-LR">Los Ríos</option>
                                    <option value="EC-MN">Manabí</option>
                                    <option value="EC-MS">Morona Santiago</option>
                                    <option value="EC-OR">Orellana</option>
                                    <option value="EC-SE">Santa Elena</option>
                                    <option value="EC-TU">Tungurahua</option>
                                </select>
                            </div>
                        </div>
                    </div>';
        $calle = '<div class="col-md-4 calle">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">  Calles y avenidas </span>
                                <input type="text" class="form-control" id="C_V" value="" name="C_V" minlength="2" required>
                            </div>
                        </div>
                    </div>';
        $ciudad = '<div class="col-md-4 ciudad">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">  Ciudad </span>
                                <input type="text" class="form-control" id="ciudad" name="ciudad" value="" minlength="3" required>
                            </div>
                        </div>
                    </div>';

        $html = '';

        $datos = PartyContactMech::where('party_contact_mech.party_id',$request->party_id)
            ->join('contact_mech as cm','party_contact_mech.contact_mech_id','cm.contact_mech_id')
            ->join('person as p','party_contact_mech.party_id','p.party_id')->get();

        foreach ($datos as $dato) {

            if($dato->contact_mech_type_id === 'EMAIL_ADDRESS')
                $email ='';
            if($dato->contact_mech_type_id === 'POSTAL_ADDRESS')
                $ciudad = ''; $provincia = ''; $calle = '';
            if($dato->contact_mech_type_id === 'TELECOM_NUMBER')
                $tlf ='';
            if(!empty($dato->nacionalidad))
                $nacionalidad = '';

        }

        $html .= $email.$tlf.$nacionalidad.$provincia.$calle.$ciudad;
       // dd($html );
        return [
            'html' => $html,
            'datosBancarios' => PaymentMethod::join('eft_account as ea','payment_method.payment_method_id','ea.payment_method_id')
            ->where([
                ['payment_method.payment_method_type_id','EFT_ACCOUNT'],
                ['payment_method.party_id',$request->party_id],
                ['payment_method.thru_date',null]
            ])->select('ea.account_number','ea.account_type','ea.codigo_banco')->first()
        ];

    }
}
