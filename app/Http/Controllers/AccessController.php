<?php

namespace App\Http\Controllers;

use App\Models\Contrataciones;
use Illuminate\Http\Request;
use App\Models\Sessions;
use App\Models\HorasExtra;
use App\Models\UserLogin;
use App\Models\ForeginContrataciones;
use DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AccessController extends Controller
{
    public function index(Request $request)
    {
        return view('layouts.views.usuario.user_profile');
    }

    public function accessUser(Request $request){

        Validator::make($request->all(), [
            'usuario' => 'required|min:4',
            'contrasena' => 'required|min:4',
       ],
       [
           'usuario.required' => 'El usuario es obligatorio.',
           'contrasena.required' => 'La contraseña es obligatoria',
           'contrasena.min' => 'La contraseña debe ser mínimo de 4 caracteres',
           'usuario.min' => 'El usuario debe ser mínimo de 4 caracteres',
       ])->validate();

        $usuario = UserLogin::where('user_login_id',$request->usuario)->first();
        if($usuario !== null){

            if($usuario->enabled === "Y"){

                if($usuario->current_password === "{SHA}".sha1($request->contrasena)){

                    $dataUser = UserLogin::join('party as py','user_login.party_id','=','py.party_id')
                    ->join('party_type as pe','py.party_type_id','=','pe.party_type_id')
                    ->join('party_identification as pn', 'py.party_id','=','pn.party_id')
                    ->join('person as p','py.party_id','=','p.party_id')
                    ->join('party_role as pr','user_login.party_id','pr.party_id')
                    ->leftJoin('party_contact_mech as pcm','py.party_id','pcm.party_id')
                    ->where([
                        ['user_login.party_id',$usuario->party_id],
                        ['pr.status',true],
                    ])->whereIn('pr.role_type_id',['ADMIN','EMPLOYEE','SUPERVISOR','NOMINA_SUPERVISOR_HE','SUPERVISOR_HORARIO'])
                    ->select(
                        'user_login.user_login_id',
                        'user_login.party_id',
                        'user_login.current_password',
                        'pe.description',
                        'pn.party_identification_type_id',
                        'pn.id_value',
                        'p.first_name',
                        'p.nacionalidad',
                        'p.last_name',
                        'p.birth_date',
                        'p.card_id',
                        'p.gender',
                        'pr.role_type_id'
                    )->get();

                    if(count($dataUser) > 0){

                        session()->forget('dataUsuario');

                        $roles = $dataUser->pluck('role_type_id')->toArray();

                        $contratacion = Contrataciones::where([
                            'estado' => '1',
                            'id_tipo_contrato_descripcion' => '2'
                        ])->first();

                       if($contratacion || (in_array('ADMIN',$roles) || in_array('SUPERVISOR',$roles) || in_array('NOMINA_SUPERVISOR_HE',$roles))){

			    	createSession($dataUser);
                            	return redirect('/');

                        }else{
                          	flash('El usuario no tiene contratos activos')->error()->important();

                        }

                    }else{
                        session()->forget('dataUsuario');
                        flash('Hacen falta datos del usuario en el sistema, verifique que roles necesarios esten asignados al usuario')->error()->important();
                    }

                }else{
                    flash('La contraseña no coincide con el usuario')->error()->important();
                }

            }else{
                session()->forget('dataUsuario');
                flash('El usuario esta deshabilitado')->error()->important();
            }

        }else{
            session()->forget('dataUsuario');
            flash('El usuario no existe')->error()->important();
        }
        return back();

    }

    public function closeSession(Request $request){

        if (session('dataUsuario')['logged']) {

            session()->forget('dataUsuario');
            Session::flush();
            DB::disconnect();

        };
        return redirect('');

    }

    public function deleteSession()
    {
        $deleteSession = Sessions::where('user_login_id',session('dataUsuario')['id_empleado']);
        $deleteSession->delete();
        session()->forget('dataUsuario');
    }
}
