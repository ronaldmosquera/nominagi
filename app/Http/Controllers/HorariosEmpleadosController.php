<?php

namespace App\Http\Controllers;

use App\Models\PartyRole;
use Illuminate\Http\Request;
use App\Models\ConfigHorarioTrabajo;
use App\Models\AsignacionHorario;
use Validator;
use DB;

class HorariosEmpleadosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('layouts.views.horarios_empleados.list',
            [
                'dataEmpleados' => PartyRole::whereIn('party_role.party_id',function ($query){
                        $query->select('party_id')->from('contrataciones as con')
                            ->where('con.estado', 1)
                            ->whereNotIn('con.party_id',[session('dataUsuario')['id_empleado']])
                            ->distinct();
                    })->join('person as p', 'party_role.party_id','=','p.party_id')
                    ->where('party_role.role_type_id','EMPLOYEE')
                    ->select('first_name','last_name','p.party_id')
                    ->get(),

            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $valida =  Validator::make($request->all(), [
            'arrData' => 'required',
        ],[
            'arrData.required' => 'Debe agregar almenos un horario'
        ]);

        if(!$valida->fails()) {
            $arrData = json_decode($request->arrData);
            AsignacionHorario::where('id_empleado',$arrData[0][3])->delete();

            foreach ($arrData as $data){

                $objAsignacionHorario = new AsignacionHorario;
                $objAsignacionHorario->id_empleado = $data[3];
                $objAsignacionHorario->fecha       = $data[2];
                $objAsignacionHorario->desde       = $data[0];
                $objAsignacionHorario->hasta       = $data[1];
                $objAsignacionHorario->clase       = $data[4];

                $existHora = AsignacionHorario::where([
                    ['fecha',$data[2]],
                    ['id_empleado',$data[3]]
                ])->count();

                if($existHora == 0){

                    if($objAsignacionHorario->save()){
                        $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                                    Horarios agregados con éxito
                                </div>';
                        $status = 1;

                    } else{
                        $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                                    Hubo un inconveniente al guardar los horarios, intente nuevamente
                                </div>';
                        $status = 0;
                    }
                }
            }
        }else{
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
            $status = 2;
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
        return view('layouts.views.horarios_empleados.partials.calendario',
            ['dataHorario' => AsignacionHorario::where('id_empleado',$request->id_empleado)->get()]);
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
    public function destroy($id)
    {
        //
    }

    public function storeIntervaloHora(Request $request){
        $valida =  Validator::make($request->all(), [
            'entrada' => 'required',
            'salida' => 'required',
            'clase' => 'required',
        ]);


        if(!$valida->fails()) {
            $objConfigHorarioTrabajo = new ConfigHorarioTrabajo;
            $objConfigHorarioTrabajo->desde = $request->entrada;
            $objConfigHorarioTrabajo->hasta = $request->salida;
            $objConfigHorarioTrabajo->clase = $request->clase;

            if($objConfigHorarioTrabajo->save()){
                $status =1;
                $msg='Guardado con exito';

            }else{
                $status =0;
                $msg='Error, intente nuevamente';
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
            $status = 2;
        }
        return response()->json(['status'=>$status,'msg'=>$msg]);
    }

    public function obtenerHorarios(){

        $dataConfigHorarios = ConfigHorarioTrabajo::all();

        $bodyHorarios='';
        foreach ($dataConfigHorarios as $configHorarios){

            $bodyHorarios .=
                '<button style="position: relative;z-index: 1; padding: 5px;color: white;opacity:1" title="Eliminar" id="'.$configHorarios->id_config_horarios_trabajo.'" onclick="eliminar_horario(this)" type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                  <div class="external-event ui-draggable ui-draggable-handle '.$configHorarios->clase.'"
                    >Desde ' .$configHorarios->desde.' hasta '.$configHorarios->hasta.'</div>';

        }
        return $bodyHorarios;

    }

    public function deleteIntervaloHora(Request $request){

        $deleteConfigHorarios = ConfigHorarioTrabajo::destroy($request->id);
        if($deleteConfigHorarios){
            $status =1;
            $msg='Eliminado con exito';

        }else{
            $status =0;
            $msg='Error, intente nuevamente';
        }
        return response()->json(['status'=>$status,'msg'=>$msg]);
    }

}
