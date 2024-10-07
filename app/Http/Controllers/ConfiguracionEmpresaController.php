<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfiguracionEmpresa;
use App\Models\ConfiguracionVariablesEmpresa;
use Validator;
use Storage;

class ConfiguracionEmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dataConfiguracionEmpresa = ConfiguracionEmpresa::all();

        $dataConfiguracionEmpresaVariables = ConfiguracionVariablesEmpresa::all();


        return view('layouts.views.configuracion_empresa.form_configuracion',
        [
            'dataConfiguracionEmpresa' => $dataConfiguracionEmpresa,
            'dataConfiguracionEmpresaVariables' => $dataConfiguracionEmpresaVariables
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
        $valida =  Validator::make($request->all(), [
            'nombre_empresa' => 'required',
            'ruc_empresa' => 'required',
            'telefono_empresa' => 'required',
            'representante' => 'required',
            'identificacion_representante' => 'required',
            'correo_representante' => 'required|email',
            'correo_empresa' => 'required|email',
            'direccion_empresa' => 'required',
        ]);

        $msg='';
        $status = '';
        $imagen= '';
        if(!$valida->fails()) {

            if($request->hasFile('imagen_empresa')){

                $validaImagen =  Validator::make($request->all(), [
                    'imagen_empresa' => 'image',
                ]);

                if(!$validaImagen->fails()) {

                    $archivo = $request->file('imagen_empresa');
                    $imagen = mt_rand().'_'.mt_rand().$archivo->getClientOriginalName();

                    Storage::disk('public')->put($imagen, \File::get($archivo));
                }

            }

            empty($request->id_config_empresa) ? $objConfigEmpresa = new ConfiguracionEmpresa : $objConfigEmpresa = ConfiguracionEmpresa::find($request->id_config_empresa);

            $objConfigEmpresa->nombre_empresa               = $request->nombre_empresa;
            $objConfigEmpresa->ruc                          = $request->ruc_empresa;
            $objConfigEmpresa->telefono                     = $request->telefono_empresa;
            $objConfigEmpresa->representante                = $request->representante;
            $objConfigEmpresa->identificacion_representante = $request->identificacion_representante;
            $objConfigEmpresa->correo_representante         = $request->correo_representante;
            $objConfigEmpresa->correo_empresa               = $request->correo_empresa;
            $objConfigEmpresa->descripcion_empresa          = $request->descrip_empresa;
            $objConfigEmpresa->direccion_empresa            = $request->direccion_empresa;
            !empty($imagen) ? $objConfigEmpresa->imagen_empresa = $imagen : '';

            if( $objConfigEmpresa->save()){

                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      Los datos se han guardado con exito
                </div>';
                $status = 1;

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
    public function destroy($id)
    {
        //
    }

    public function storeConfiguracionVariables(Request $request){

        $valida =  Validator::make($request->all(), [
            'entre_semana'                      => 'required',
            'fin_semana'                        => 'required',
            'sbuv'                              => 'required',
            'vacaciones_dias_entre_semana'      => 'required',
            'vacaciones_dias_fines_semana'      => 'required',
            'porcentaje_avance'                 => 'required',
            'diferir_consumos_meses'            => 'required',
            'iva'                               => 'required',
            'aporte_patronal'                   => 'required',
            'aporte_personal'                   => 'required',
            'fondo_reserva'                     => 'required',
            //'entre_semana_relacion_dependencia' => 'required',
            //'fin_semana_relacion_dependencia'   => 'required',
            'anno_calculo_fondo_reserva'        => 'required',
            'antiguedad'                        => 'required',
            'fecha_hasta'                       => 'required',
            'intervalo'                         => 'required',
            ''
        ]);

        $msg='';
        $status = '';
        if(!$valida->fails()) {

            empty($request->id_configuracion_variables) ? $objConfigVariablesEmpresa = new ConfiguracionVariablesEmpresa : $objConfigVariablesEmpresa = ConfiguracionVariablesEmpresa::find($request->id_configuracion_variables);

            $model = ConfiguracionEmpresa::first();
            $objConfigVariablesEmpresa->id_configuracion_empresa                      = $model->id_configuracion_empresa;
            $objConfigVariablesEmpresa->hora_extra_entre_semana                       = $request->entre_semana;
            $objConfigVariablesEmpresa->hora_extra_fin_semana                         = $request->fin_semana;
            $objConfigVariablesEmpresa->sueldo_basico_unificado_vigente               = $request->sbuv;
            $objConfigVariablesEmpresa->vacaciones_dias_entre_semana                  = $request->vacaciones_dias_entre_semana;
            $objConfigVariablesEmpresa->vacaciones_dias_fines_semana                  = $request->vacaciones_dias_fines_semana;
            $objConfigVariablesEmpresa->porcentaje_avance                             = $request->porcentaje_avance;
            $objConfigVariablesEmpresa->diferir_consumos_meses                        = $request->diferir_consumos_meses;
            $objConfigVariablesEmpresa->iva                                           = $request->iva;
            $objConfigVariablesEmpresa->aporte_patronal                               = $request->aporte_patronal;
            $objConfigVariablesEmpresa->aporte_personal                               = $request->aporte_personal;
            $objConfigVariablesEmpresa->fondo_reserva                                 = $request->fondo_reserva;
            //$objConfigVariablesEmpresa->hora_extra_entre_semana_relacion_dependencia  = $request->entre_semana_relacion_dependencia;
            //$objConfigVariablesEmpresa->hora_extra_fin_semana_relacion_dependencia    = $request->fin_semana_relacion_dependencia;
            $objConfigVariablesEmpresa->anno_calculo_fondo_reserva                    = $request->anno_calculo_fondo_reserva;
            $objConfigVariablesEmpresa->antiguedad                                    = $request->antiguedad;
            $objConfigVariablesEmpresa->fecha_hasta                                   = $request->fecha_hasta;
            $objConfigVariablesEmpresa->intervalo                                     = $request->intervalo;
            $objConfigVariablesEmpresa->tiempo_carga_he                               = $request->tiempo_carga_he;
            $objConfigVariablesEmpresa->tiempo_aprov_he                               = $request->tiempo_aprov_he;

            if( $objConfigVariablesEmpresa->save()){

                $msg = '<div class="alert alert-success" role="alert" style="margin: 0">
                      Los datos se han guardado con exito
                </div>';
                $status = 1;

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
