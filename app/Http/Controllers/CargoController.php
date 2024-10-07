<?php

namespace App\Http\Controllers;

use App\Models\DetalleContratacion;
use Illuminate\Http\Request;
use App\Models\Cargo;
use App\Models\ConfiguracionVariablesEmpresa;
use Validator;

class CargoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('layouts.views.cargo.list_cargos',
            [
                'dataCargos'=> Cargo::all()
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       //return view();
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
            'cargo'                   => 'required',
            'descripcion'             => 'required',
            'sueldo_minimo_sectorial' => 'required'
        ]);

        $msg='';
        if(!$valida->fails()) {

            empty($request->id_cargo) ? $objCargo = new Cargo : $objCargo = Cargo::find($request->id_cargo);

            $objCargo->nombre                  = $request->cargo;
            $objCargo->descripcion             = $request->descripcion;
            $objCargo->sueldo_minimo_sectorial = $request->sueldo_minimo_sectorial;
            $objCargo->cargo_confianza         = $request->cargo_confianza;

            if ($objCargo->save()) {
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      Cargo agregado con éxito
                </div>';
                $status = 1;

            } else{
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      Hubo un inconveniente al guardar los datos, intente nuevamente
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return ConfiguracionVariablesEmpresa::select('sueldo_basico_unificado_vigente')->first()->sueldo_basico_unificado_vigente;
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

    public function deleteCargo(Request $request)
    {
        $countContrataciones = DetalleContratacion::where([
            ['id_cargo',$request->id_cargo],
            ['estado',1]
        ])->join('contrataciones as cont','detalles_contrataciones.id_contrataciones','cont.id_contrataciones')
            ->count();

        if($countContrataciones > 0 ){
            $msg = '<div class="alert alert-info" role="alert" style="margin: 0">
                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                    El cargo no puede ser eliminado por que está en uso
                    </div>';
        }else{
            $dataCargo = Cargo::destroy($request->id_cargo);
            if($dataCargo){
                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                          Cargo Eliminado con éxito
                        </div>';
            }else{
                $msg = '<div class="alert alert-danger" role="alert" style="margin: 0">
                          Hubo un error al eliminar el cargo, intente nuevamente
                        </div>';
            }
        }
        return $msg;
    }

    public function vistaFormContrato(Request $request){


        return view('layouts.views.cargo.partials.form_cargos',['dataCargo' => Cargo::where('id_cargo',$request->id_cargo)->first()]);
    }
}
