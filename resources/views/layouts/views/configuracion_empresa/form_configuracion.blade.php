@extends('layouts.principal')
@section('title')
    Configuración de empresa
@endsection

@section('content')
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#datos" data-toggle="tab" aria-expanded="true">Datos de empresa</a></li>
                <li class=""><a href="#variables" data-toggle="tab" aria-expanded="true">Configuración de variables</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="datos">
                    <!-- Post -->
                    <div class="">
                        <div class="box-header with-border">
                            <h3 class="box-title">Información importante</h3>
                        </div>
                        <form id="form_configuracion" name="form_configuracion" enctype="multipart/form-data" novalidate>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Empresa</span>
                                            <input type="text" name="nombre_empresa" id="nombre_empresa" value="{!! isset($dataConfiguracionEmpresa[0]->nombre_empresa) ?  $dataConfiguracionEmpresa[0]->nombre_empresa : '' !!}" class="form-control" required minlength="3">
                                            <input type="hidden" id="id_config_empresa" name="id_config_empresa" value="{!! isset($dataConfiguracionEmpresa[0]->id_configuracion_empresa) ?  $dataConfiguracionEmpresa[0 ]->id_configuracion_empresa : '' !!}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">RUC</span>
                                            <input type="text" name="ruc_empresa" id="ruc_empresa" class="form-control" required minlength="8" value="{!! isset($dataConfiguracionEmpresa[0]->ruc) ?  $dataConfiguracionEmpresa[0 ]->ruc : '' !!}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group ">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Teléfono</span>
                                            <input type="number" name="telefono_empresa" id="telefono_empresa" class="form-control"  required minlength="7" value="{!! isset($dataConfiguracionEmpresa[0]->telefono) ?  $dataConfiguracionEmpresa[0 ]->telefono : '' !!}">
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group ">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Correo Empresa</span>
                                            <input type="email" class="form-control" name="correo_empresa" id="correo_empresa" required minlength="11" value="{!! isset($dataConfiguracionEmpresa[0]->correo_empresa) ?  $dataConfiguracionEmpresa[0 ]->correo_empresa : '' !!}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group ">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Imagen Empresa</span>
                                            <input type="file" class="form-control" name="imagen_empresa" id="imagen_empresa" accept="image/.jpg,.png,.JPG,.PNG">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group ">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Dirección</span>
                                            <input type="text" name="direccion_empresa" id="direccion_empresa" class="form-control"  required minlength="7" value="{!! isset($dataConfiguracionEmpresa[0]->direccion_empresa) ?  $dataConfiguracionEmpresa[0 ]->direccion_empresa : '' !!}">
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group ">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Representante</span>
                                            <input type="text" name="representante" id="representante" class="form-control" required minlength="3" value="{!! isset($dataConfiguracionEmpresa[0]->representante) ?  $dataConfiguracionEmpresa[0 ]->representante : '' !!}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group ">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Identificación</span>
                                            <input type="number" name="identificacion_representante" id="identificacion_representante" class="form-control" required minlength="7" value="{!! isset($dataConfiguracionEmpresa[0]->identificacion_representante) ?  $dataConfiguracionEmpresa[0 ]->identificacion_representante : '' !!}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group ">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Correo</span>
                                            <input type="mail" name="correo_representante" id="correo_representante" class="form-control" required minlength="11" value="{!! isset($dataConfiguracionEmpresa[0]->correo_representante) ?  $dataConfiguracionEmpresa[0 ]->correo_representante : '' !!}">
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <div class="row">
                                    <div class="col-md-12">
                                        <div style="margin-top: 10px">
                                            <label>Descripción de la empresa</label>
                                            <textarea class="ckeditor" name="descrip_empresa"  id="descrip_empresa" rows="10" cols="80">{!! isset($dataConfiguracionEmpresa[0]->descripcion_empresa) ?  $dataConfiguracionEmpresa[0 ]->descripcion_empresa : '' !!}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-block btn-success btn-lg" onclick="store_configuracion()"><i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i> Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane" id="variables">
                    <!-- Post -->
                    <div class="">
                        <form id="form_configuracion_variables" name="form_configuracion_variables" novalidate>
                            <input type="hidden" id="id_configuracion_variables" name="id_configuracion_variables"
                                    value="{!! isset($dataConfiguracionEmpresaVariables[0]->id_configuracion_empresa_variables) ?  $dataConfiguracionEmpresaVariables[0]->id_configuracion_empresa_variables : '' !!}">
                            <div class="box-header with-border">
                                <h3 class="box-title">Pago horas extras</h3>
                            </div>
                            <div class="box-body">
                                <div class="row" style="padding: 0px 0 20px;">
                                    {{--<div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Entre semana (bajo relación de dependencia) $</span>
                                            <input type="text" onkeypress="return filterFloat(event,this)" name="entre_semana_relacion_dependencia" placeholder="Ej: 3.5" id="entre_semana_relacion_dependencia"
                                                    value="{!! isset($dataConfiguracionEmpresaVariables[0]->hora_extra_entre_semana_relacion_dependencia) ?  $dataConfiguracionEmpresaVariables[0]->hora_extra_entre_semana_relacion_dependencia : '' !!}" class="form-control" required >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Fines de semana (bajo relación de dependencia) $</span>
                                            <input type="text" name="fin_semana_relacion_dependencia" id="fin_semana_relacion_dependencia" class="form-control" placeholder="Ej: 3.5"
                                                    onkeypress="return filterFloat(event,this)" required value="{!! isset($dataConfiguracionEmpresaVariables[0]->hora_extra_fin_semana_relacion_dependencia) ?  $dataConfiguracionEmpresaVariables[0]->hora_extra_fin_semana_relacion_dependencia : '' !!}">
                                        </div>
                                    </div>--}}
                                    <div class="col-md-6" style="margin-top: 20px">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Entre semana (Sin relación de dependencia) $</span>
                                            <input type="text"  name="entre_semana" placeholder="Ej: 3.5" id="entre_semana"
                                                    value="{!! isset($dataConfiguracionEmpresaVariables[0]->hora_extra_entre_semana) ?  $dataConfiguracionEmpresaVariables[0]->hora_extra_entre_semana : '' !!}" class="form-control" required >
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 20px">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Fines de semana (Sin relación de dependencia) $</span>
                                            <input type="text" name="fin_semana" id="fin_semana" class="form-control" placeholder="Ej: 3.5"
                                                    required value="{!! isset($dataConfiguracionEmpresaVariables[0]->hora_extra_fin_semana) ?  $dataConfiguracionEmpresaVariables[0]->hora_extra_fin_semana : '' !!}">
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 20px">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Tiempo para cargar horas extras (Días)</span>
                                            <input type="text" name="tiempo_carga_he" id="tiempo_carga_he" class="form-control" placeholder="Ej: 3"
                                                    required value="{!! isset($dataConfiguracionEmpresaVariables[0]->tiempo_carga_he) ?  $dataConfiguracionEmpresaVariables[0]->tiempo_carga_he : '' !!}">
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 20px">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Tiempo para aprobar horas extras (Días)</span>
                                            <input type="text" name="tiempo_aprov_he" id="tiempo_aprov_he" class="form-control" placeholder="Ej: 3"
                                                    required value="{!! isset($dataConfiguracionEmpresaVariables[0]->tiempo_aprov_he) ?  $dataConfiguracionEmpresaVariables[0]->tiempo_aprov_he : '' !!}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-header with-border">
                                <h3 class="box-title">Vacaciones (Días) y % de avance de sueldo en base al salario neto del empleado</h3>
                            </div>
                            <div class="box-body">
                                <div class="row" style="padding: 0px 0 20px;">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Entre semana (Días)</span>
                                            <input type="text" name="vacaciones_dias_entre_semana" placeholder="11" id="vacaciones_dias_entre_semana"
                                                    value="{!! isset($dataConfiguracionEmpresaVariables[0]->vacaciones_dias_entre_semana) ?  $dataConfiguracionEmpresaVariables[0]->vacaciones_dias_entre_semana : '' !!}" class="form-control" required >
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Fines de semana (Días)</span>
                                            <input type="text" name="vacaciones_dias_fines_semana" id="vacaciones_dias_fines_semana" class="form-control" placeholder="4"
                                                    required value="{!! isset($dataConfiguracionEmpresaVariables[0]->vacaciones_dias_fines_semana) ?  $dataConfiguracionEmpresaVariables[0]->vacaciones_dias_fines_semana : '' !!}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">% de avance de sueldo</span>
                                            <input type="text" name="porcentaje_avance" id="porcentaje_avance" class="form-control"
                                                    required value="{!! isset($dataConfiguracionEmpresaVariables[0]->porcentaje_avance) ?  $dataConfiguracionEmpresaVariables[0]->porcentaje_avance : '' !!}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-header with-border">
                                <h3 class="box-title">Tiempo máximo para diferir consumos, IVA y Sueldo básico unificado vigente</h3>
                            </div>
                            <div class="box-body">
                                <div class="row" style="padding: 0px 0 20px;">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Cantidad (meses)</span>
                                            <input type="text"  name="diferir_consumos_meses" id="diferir_consumos_meses"
                                                    value="{!! isset($dataConfiguracionEmpresaVariables[0]->diferir_consumos_meses) ?  $dataConfiguracionEmpresaVariables[0]->diferir_consumos_meses : '' !!}" class="form-control" required >
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">IVA %</span>
                                            <input type="text" name="iva" id="iva"
                                                    value="{!! isset($dataConfiguracionEmpresaVariables[0]->iva) ?  $dataConfiguracionEmpresaVariables[0]->iva : '' !!}" class="form-control" required >
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">SBUV</span>
                                            <input type="text" name="sbuv" id="sbuv" class="form-control" placeholder="3.5"
                                                    required value="{!! isset($dataConfiguracionEmpresaVariables[0]->sueldo_basico_unificado_vigente) ?  $dataConfiguracionEmpresaVariables[0]->sueldo_basico_unificado_vigente : '' !!}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-header with-border">
                                <h3 class="box-title">Aportes al IESS y Fondo de reserva</h3>
                            </div>
                            <div class="box-body">
                                <div class="row" style="padding: 0px 0 20px;">
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Aportes patronales %</span>
                                            <input type="text"  name="aporte_patronal" id="aporte_patronal"
                                                    value="{!! isset($dataConfiguracionEmpresaVariables[0]->aporte_patronal) ?  $dataConfiguracionEmpresaVariables[0]->aporte_patronal : '' !!}" class="form-control" required >
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Aportes personales %</span>
                                            <input type="text"  name="aporte_personal" id="aporte_personal"
                                                    value="{!! isset($dataConfiguracionEmpresaVariables[0]->aporte_personal) ?  $dataConfiguracionEmpresaVariables[0]->aporte_personal : '' !!}" class="form-control" required >
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Fondo de reserva %</span>
                                            <input type="text"  name="fondo_reserva" id="fondo_reserva"
                                                    value="{!! isset($dataConfiguracionEmpresaVariables[0]->fondo_reserva) ?  $dataConfiguracionEmpresaVariables[0]->fondo_reserva : '' !!}" class="form-control" required >

                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Años para calcular el fondo de reserva</span>
                                            <input type="text"  name="anno_calculo_fondo_reserva" id="anno_calculo_fondo_reserva"
                                                    value="{!! isset($dataConfiguracionEmpresaVariables[0]->anno_calculo_fondo_reserva) ?  $dataConfiguracionEmpresaVariables[0]->anno_calculo_fondo_reserva : '' !!}" class="form-control" required >
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                </div>
                            </div>
                            <div class="box-header with-border">
                                <h3 class="box-title">Políticas para anticipos</h3>
                            </div>
                            <div class="box-body">
                                <div class="row" style="padding: 0px 0 20px;">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Antiguedad de contrato (Meses)</span>
                                            <input type="number" min="1"  name="antiguedad" id="antiguedad"
                                                    value="{!! isset($dataConfiguracionEmpresaVariables[0]->antiguedad) ?  $dataConfiguracionEmpresaVariables[0]->antiguedad : '' !!}" class="form-control" required >
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Día tope para solictud</span>
                                            <input type="number" min="1"  name="fecha_hasta" id="fecha_hasta"
                                                    value="{!! isset($dataConfiguracionEmpresaVariables[0]->fecha_hasta) ?  $dataConfiguracionEmpresaVariables[0]->fecha_hasta : '' !!}" class="form-control" required >
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #D9D9D9;">Intervalo de tiempo para solicitud (Meses)</span>
                                            <input type="number" min="1"  name="intervalo" id="intervalo"
                                                    value="{!! isset($dataConfiguracionEmpresaVariables[0]->intervalo) ?  $dataConfiguracionEmpresaVariables[0]->intervalo : '' !!}" class="form-control" required >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 20px">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-block btn-success btn-lg" onclick="store_configuracion_varibles()">
                                        <i id="ico2" class="fa fa-floppy-o" aria-hidden="true" ></i> Guardar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_page_js')
    @include('layouts.views.configuracion_empresa.script')
@endsection
