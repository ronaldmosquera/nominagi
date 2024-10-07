@extends('layouts.principal')
@section('title')
    Crear contratación
@endsection

@section('content')
    <form id="form_add_contratacion" name="form_add_contratacion">
        <input type="hidden" value="{{$dataContratacion != null ? $dataContratacion->id_contrataciones : '' }}" id="id_contratacion">
        <input type="hidden" value="{{$dataEmpleados != null ? $dataEmpleados->party_id : '' }}" id="party_id">
        <input type="hidden" value="{{$dataEmpleados != null ? $dataEmpleados->contact_mech_id : '' }}" id="contact_mech_id">
        <input type="hidden" value="{{$dataEmpleados != null ? $dataEmpleados->party_id_contact : '' }}" id="party_id_contact">
        <input type="hidden" value="{{$dataContratacion != null ? $dataContratacion->id_detalle_contrataciones : '' }}" id="id_detalle_contrataciones">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">{{$dataContratacion != null ? "Editar" : "Crear" }} contratación</h3>
                    </div>
                    <div class="row" style="padding: 0px 20px">
                        <div class="col-md-12" >
                            @if($dataContratacion === null)
                            <div class="col-md-2" style="padding: 0">
                              <input type="checkbox" id="tipo_empleado" name="tipo_empleado" style="position:relative;top: 3px;" onchange="tipo_usuario()"><label for="tipo_empleado"> ¿Usuario nuevo? </label>
                            </div>
                            @endif
                            <div class="col-md-4" style="padding: 0">
                              <input type="checkbox" id="activa" name="activa" style="position:relative;top: 3px;" ><label for="activa"> {{$dataContratacion === null ? " ¿Activar automáticamente?" : ' ¡Activar!'}} </label>
                            </div>
                        </div>
                        <div class="{{$dataContratacion === null ? "col-md-4" : "col-md-6"}}" id="div_contrato">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> <i class="fa fa-file-text" aria-hidden="true"></i> Contrato</span>
                                    <select class="form-control" id="id_tipo_contrato" name="id_tipo_contrato" {{$dataEmpleados != null ? "disabled='disabled'" : ''}} onchange="cuerpo_contrato()" required>
                                        <option selected disabled>Seleccione</option>
                                        @foreach($dataTipoContratos as $tipoContratos)
                                            <option
                                                    @php
                                                        $selected = '';
                                                        if($dataContratacion != null && $tipoContratos->id_tipo_contrato == $dataContratacion->id_tipo_contrato)
                                                            $selected = "selected='selected'";
                                                    @endphp
                                                       {{$selected}} value="{{$tipoContratos->id_tipo_contrato}}">{{$tipoContratos->nombre}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @if($dataContratacion === null)
                            <div class="col-md-4" id="select_empleados">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> <i class="fa fa-user"></i> Empleado</span>
                                        <select class="form-control" id="id_empleado" name="id_empleado" required onchange="search_datos_faltantes()">
                                            <option selected disabled> Seleccione </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endif
                        <div id="campos_obligatorios">
                            @if($dataContratacion !== null)
                                @if($dataContratacion->id_tipo_contrato_descripcion == 2)
                                    <div class="col-md-6" id="div_salario">
                                        <div class="form-group">
                                            <div class="input-group salario">
                                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                    <i class="fa fa-money" aria-hidden="true"></i> Salario $</span>
                                                <input type="number" min="{{ $sueldo_sectorial > 0 ? $sueldo_sectorial : 1}}" class="form-control" id="salario" name="salario"
                                                   value="{{$dataContratacion->salario}}" placeholder="Ej: 400.00"  onkeyup="converitr_letras('{{$sueldo_sectorial}}',this.value)" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="padding: 0px 15px">
                                        <div class="col-md-12" id="div_salario">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                        <i class="fa fa-money" aria-hidden="true"></i> Salario en letras
                                                    </span>
                                                    <input type="text"  class="form-control" id="letras" name="letras" readonly="true"
                                                           placeholder="Ej: 400.00"  onkeypress="return filterFloat(event,this)" required>
                                                </div>
                                            </div>
                                        </div>
                                        @if(!$dataContratacion->relacion_dependencia)
                                            <div class="col-md-3" >
                                                <div class="form-group">
                                                    <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                                <i class="fa fa-file-text-o" aria-hidden="true"></i> TIPO DOCUMENTO
                                                            </span>
                                                        <select  class="form-control" id="tipo_documento" name="tipo_documento" required onchange="select_retencion()">
                                                            @foreach ($tipoDocumentos as $td)
                                                                <option {{isset($dataContratacion->tipo_documento) && $dataContratacion->tipo_documento == $td->invoice_type_id ? 'selected' : ''}}
                                                                        value='{{$td->invoice_type_id}}'>
                                                                    {{$td->description}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4" >
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                            <i class="fa fa-check" aria-hidden="true"></i> TIPO RETENCIÓN IVA
                                                        </span>
                                                        <select class="form-control" id="tipo_impuesto_iva" name="tipo_impuesto_iva"
                                                                onchange="$('#retencion_iva').val($('#tipo_impuesto_iva').val().split('*')[0])">
                                                                <option value="">Seleccione</option>
                                                            @foreach ($tipoImpuestos->filter(function($obj){ return $obj->tipo_impuesto== 'IVA';}) as $tipoIva)
                                                                <option {{isset($dataContratacion->tipo_retencion_iva) && explode('*',$dataContratacion->tipo_retencion_iva)[1] == $tipoIva->deduction_type_id ? 'selected' : ''}}
                                                                        value='{{$tipoIva->porcentaje_impuesto}}*{{$tipoIva->deduction_type_id}}*{{$tipoIva->gl_account_id}}*{{$tipoIva->tipo_impuesto}}'>
                                                                    {{$tipoIva->description}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5" >
                                                <div class="form-group">
                                                    <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                                <i class="fa fa-check" aria-hidden="true"></i> TIPO RETENCIÓN RENTA
                                                            </span>
                                                        <select class="form-control" id="tipo_impuesto_renta" name="tipo_impuesto_renta"
                                                                onchange="$('#retencion_renta').val($('#tipo_impuesto_renta').val().split('*')[0])">
                                                                <option value="">Seleccione</option>
                                                            @foreach ($tipoImpuestos->filter(function($obj){ return $obj->tipo_impuesto== 'RENTA';}) as $tipoRenta)
                                                                <option {{isset($dataContratacion->tipo_retencion_renta) && explode('*',$dataContratacion->tipo_retencion_renta)[1] == $tipoRenta->deduction_type_id ? 'selected' : ''}}
                                                                        value='{{$tipoRenta->porcentaje_impuesto}}*{{$tipoRenta->deduction_type_id}}*{{$tipoRenta->gl_account_id}}*{{$tipoRenta->tipo_impuesto}}'>
                                                                    {{$tipoRenta->description}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4" >
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                            <i class="fa fa-money" aria-hidden="true"></i> IVA
                                                        </span>
                                                        <select class="form-control" id="iva" name="iva" required>
                                                            <option selected disabled value="">Seleccione</option>
                                                            @foreach($iva as $i)
                                                                <option {{$dataContratacion->iva == $i->valor ? 'selected' : ''}} value="{{$i->valor}}">{{$i->nombre}}</option>
                                                            @endforeach
                                                        </select>
                                                        {{--<input type="number"  class="form-control" id="iva" name="iva"
                                                               placeholder="Ej: 12%"  onkeypress="return filterFloat(event,this)" value="{{$dataContratacion->iva}}" required>--}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4" >
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                            <i class="fa fa-money" aria-hidden="true"></i> RETENCIÓN IVA %
                                                        </span>
                                                        <input type="number"  class="form-control" id="retencion_iva" name="retencion_iva"
                                                               value="{{$dataContratacion->retencion_iva}}" placeholder="Ej: 10%"  onkeypress="return filterFloat(event,this)" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4" >
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                            <i class="fa fa-money" aria-hidden="true"></i> RETENCIÓN RENTA3 %
                                                        </span>
                                                        <input type="number"  class="form-control" id="retencion_renta" name="retencion_renta"
                                                               value="{{$dataContratacion->retencion_renta}}"  placeholder="Ej: 10%"  onkeypress="return filterFloat(event,this)" required>
                                                    </div>
                                                </div>
                                            </div>

                                        @endif
                                    </div>
                                    <div class="row" style="padding: 0px 15px" id="fecha_cargo">
                                        {{--<div class="col-md-6">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon"  style="background: #d9d9d9;"> <i class="fa fa-calendar"></i> Fechas</span>
                                                    <input type="text" class="form-control datepicker" id="fecha_horario" name="fecha_horario"   required style="border-radius: 0px">
                                                </div>
                                            </div>
                                        </div>--}}
                                        <div class="col-md-{{isset($dataContratacion->caducidad) && $dataContratacion->caducidad ? '3' : '4'}}">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon"  style="background: #d9d9d9;"> <i class="fa fa-calendar"></i> Inicio</span>
                                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{$dataContratacion->fecha_expedicion_contrato}}" required style="border-radius: 0px">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-{{isset($dataContratacion->caducidad) && $dataContratacion->caducidad ? '3' : '4'}}">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon"  style="background: #d9d9d9;"> <i class="fa fa-clock-o"></i> Horas laborales</span>
                                                    <input type="number" value="{{$dataContratacion->horas_jornada_laboral}}" min="1" class="form-control" id="horas" name="horas" onkeypress="return filterFloat(event,this)" required style="border-radius: 0px">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-{{isset($dataContratacion->caducidad) && $dataContratacion->caducidad ? '3' : '4'}}">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"><i class="fa fa-user-circle-o" aria-hidden="true"></i> Cargo </span>
                                                    <select class="form-control" id="id_cargo" name="id_cargo" required>
                                                        <option disabled selected>Seleccione</option>
                                                        @foreach($dataCargos as $cargo)
                                                            @php
                                                                $selected = '';
                                                                if($dataContratacion->id_cargo == $cargo->id_cargo)
                                                                    $selected = "selected='selected'";
                                                            @endphp
                                                            <option {{$selected}} value="{{$cargo->id_cargo}}">{{$cargo->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        @if(isset($dataContratacion->caducidad) && $dataContratacion->caducidad)
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"  style="background: #d9d9d9;"> <i class="fa fa-calendar-check-o" aria-hidden="true"></i> Duración (dias)</span>
                                                        <input type="number" class="form-control" id="cant_dias" name="cant_dias" min="1" onkeypress="return filterFloat(event,this)" value="{{$dataContratacion->duracion}}" required style="border-radius: 0px">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if($dataContratacion->relacion_dependencia)
                                        <div class="row" id="div_campos_relacion_dependencia" style="padding: 0px 15px">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                            <i class="fa fa-money" aria-hidden="true"></i>
                                                            Decimo tercero
                                                        </span>
                                                        <select id="decimo_tercero"  name="decimo_tercero" class="form-control" required>
                                                            <option disabled selected>Seleccione</option>
                                                            <option value="1" {{$dataContratacion->decimo_tercero == true ? "selected='selected'" : ''}}> Mensualizado</option>
                                                            <option value="0" {{$dataContratacion->decimo_tercero == false ? "selected='selected'" : ''}}> Anualizado</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                            <i class="fa fa-money" aria-hidden="true"></i>
                                                            Decimo cuarto
                                                        </span>
                                                        <select id="decimo_cuarto"  name="decimo_cuarto" class="form-control" required>
                                                            <option disabled selected>Seleccione</option>
                                                            <option value="1" {{$dataContratacion->decimo_cuarto == true ? "selected='selected'" : ''}}> Mensualizado</option>
                                                            <option value="0" {{$dataContratacion->decimo_cuarto == false ? "selected='selected'" : ''}}> Anualizado</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                            <i class="fa fa-money" aria-hidden="true"></i>
                                                            Fondo de reserva
                                                        </span>
                                                        <select id="fondo_reserva"  name="fondo_reserva" class="form-control" required>
                                                            <option disabled selected>Seleccione</option>
                                                            <option value="1" {{$dataContratacion->fondo_reserva == true ? "selected='selected'" : ''}}> Mensualizado</option>
                                                            <option value="0" {{$dataContratacion->fondo_reserva == false ? "selected='selected'" : ''}}> Anualizado</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                        <input type="hidden" id="id_empleado" value="{!! isset($dataEmpleados->party_id) ? $dataEmpleados->party_id : '' !!}">
                                        <div class="">
                                            <div class="">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> Nombres </span>
                                                            <input type="text"  class="form-control" id="nombres" name="nombres"
                                                                   value="{!! isset($dataEmpleados->first_name) ? $dataEmpleados->first_name : '' !!}" minlength="3" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">  Apellidos</span>
                                                            <input type="text" class="form-control" id="apellidos" name="apellidos"
                                                                   value="{!! isset($dataEmpleados->last_name) ? $dataEmpleados->last_name : '' !!}" minlength="3" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> F. nacimiento </span>
                                                            <input type="date" class="form-control" id="nacimiento"
                                                                   value="{!! isset($dataEmpleados->birth_date) ? $dataEmpleados->birth_date : '' !!}"
                                                                   name="nacimiento" minlength="3" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> Género </span>
                                                            <select class="form-control" id="genero" name="genero" required>
                                                                <option disabled selected>Seleccione</option>
                                                                <option value="M" {!! isset($dataEmpleados->gender) ? $dataEmpleados->gender == 'M' ? "selected='selected'" : '' : '' !!}>Masculino</option>
                                                                <option value="F" {!! isset($dataEmpleados->gender) ? $dataEmpleados->gender == 'F' ? "selected='selected'" : '' : '' !!}>Femenino</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> Tipo ID </span>
                                                            <select class="form-control" id="tipo_identificacion" name="tipo_identificacion">
                                                                <option disabled selected>Seleccione</option>
                                                                @foreach($dataTipoIdentificacionGrupo as $tipoIdentificacionGrupo)
                                                                    <option
                                                                            {!! isset($dataEmpleados->party_identification_type_id)
                                                                               ? $dataEmpleados->party_identification_type_id == $tipoIdentificacionGrupo->party_identification_type_id
                                                                                   ? "selected='selected'" : ''
                                                                               : '' !!}
                                                                            value="{{$tipoIdentificacionGrupo->party_identification_type_id}}">{{$tipoIdentificacionGrupo->description}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> N° Identificación </span>
                                                            <input type="text" class="form-control" id="identificacion" onkeypress="return filterFloat(event,this)"
                                                                   value="{!! isset($dataEmpleados->id_value) ? $dataEmpleados->id_value : '' !!}" name="identificacion" minlength="5" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> E-mail </span>
                                                            <input type="email" class="form-control" id="correo"  name="correo"
                                                                   value="{!! isset($dataEmpleados->party_id) ? (isset(getEmail($dataEmpleados->party_id)->info_string) ? getEmail($dataEmpleados->party_id)->info_string : '') : "" !!}"  minlength="11" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">  Teléfono </span>
                                                            <input type="text" class="form-control" id="telefono" onkeypress="return filterFloat(event,this)"
                                                                   value="{!! isset($dataEmpleados->party_id) && isset(getTelecomNumber($dataEmpleados->party_id)->contact_number) ? getTelecomNumber($dataEmpleados->party_id)->contact_number : '' !!}" name="telefono" minlength="7" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">  Nacionalidad </span>
                                                            <input type="text" class="form-control" id="nacionalidad" name="nacionalidad"
                                                                   value="{!! isset($dataEmpleados->nacionalidad) ? $dataEmpleados->nacionalidad : '' !!}"  minlength="2" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> Provincia </span>
                                                            <select class="form-control" id="id_provincia" name="id_provincia">
                                                                <option disabled selected>Seleccione</option>
                                                                @foreach($dataProvinicias as $provinicia)
                                                                        <option
                                                                            {!! isset($dataEmpleados->party_id) && isset(getPostalAddres($dataEmpleados->party_id)->state_province_geo_id)
                                                                                ? getPostalAddres($dataEmpleados->party_id)->state_province_geo_id == $provinicia->geo_id
                                                                                   ? "selected='selected'" : ''
                                                                               : '' !!}
                                                                            value="{{$provinicia->geo_id}}">{{$provinicia->geo_name}}
                                                                        </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">  Calles y avenidas </span>
                                                            <input type="text" class="form-control" id="C_V"
                                                                   value="{!! isset($dataEmpleados->party_id) && isset(getPostalAddres($dataEmpleados->party_id)->address1) ? getPostalAddres($dataEmpleados->party_id)->address1 : '' !!}" name="C_V" minlength="2" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">  Ciudad </span>
                                                            <input type="text" class="form-control" id="ciudad" name="ciudad"
                                                                   value="{!! isset($dataEmpleados->party_id) && isset(getPostalAddres($dataEmpleados->party_id)->city) ? getPostalAddres($dataEmpleados->party_id)->city : '' !!}" minlength="3" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> Nombres Contacto </span>
                                                            <input type="text" class="form-control" id="nombre_contacto"  name="nombre_contacto"
                                                                   value="{!! isset($dataEmpleados->party_id) && isset($dataEmpleados->first_name_contact) ? $dataEmpleados->first_name_contact : '' !!}"  minlength="3" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">  Apellidos Contacto </span>
                                                            <input type="text" class="form-control" id="apellido_contacto"  name="apellido_contacto"
                                                                   value="{!! isset($dataEmpleados->last_name_contact) ? $dataEmpleados->last_name_contact : '' !!}"  minlength="3">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">  Tlf Contacto (Movíl) </span>
                                                            <input type="tel" class="form-control" id="telefono_contacto"
                                                                   value="{!! isset($dataEmpleados) && isset(getTelecomNumber($dataEmpleados->party_id_contact)->contact_number) ? getTelecomNumber($dataEmpleados->party_id_contact)->contact_number : '' !!}"
                                                                   onkeypress="return filterFloat(event,this)"  name="telefono_contacto" minlength="7">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                @else
                                    <div class="col-md-6" id="input_empleados">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> <i class="fa fa-user"></i> Empleado2</span>
                                                <input type="text" class="form-control" id="nombre_empleado" name="nombre_empleado" value="{{$dataEmpleados->first_name." ".$dataEmpleados->last_name}}" required>
                                                <input type="hidden" class="form-control" id="id_empleado" name="id_empleado" value="{{$dataEmpleados->party_id}}" required>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                        <div id="campos_relacion_dependencia"></div>
                    </div>
                    <div class="hide" style="padding: 0px 20px;" id="datos_empleados"></div>
                    <div class="col-md-12" style="padding: 0px 20px">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                            Ciudad de contratación
                                        </span>
                                        <select id="id_ciudad" name="id_ciudad" class="form-control" required>
                                            <option {{isset($dataContratacion) && $dataContratacion->id_ciudad == 'QUITO' ? 'selected': ''}}>QUITO</option>
                                            <option {{isset($dataContratacion) && $dataContratacion->id_ciudad == 'GUAYAQUIL' ? 'selected': ''}}>GUAYAQUIL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                             Funciones del empleado</span>
                                        <input id="funciones" name="funciones" class="form-control" value="{{isset($dataContratacion) ? $dataContratacion->funciones :'' }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="padding: 0px 20px">
                        <em class="error">Datos bancarios del empleado *</em>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">Banco</span>
                                        <select id="id_banco" name="id_banco" class="form-control" required>
                                            <option value="">Seleccione</option>
                                            @foreach ($bancos as $item)
                                                <option {{isset($dataEmpleados) && $dataEmpleados->codigo_banco == $item->enum_id ? 'selected': ''}}
                                                    value="{{$item->enum_id}}">
                                                    {{$item->description}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">Tipo cuenta</span>
                                        <select id="tipo_cuenta" name="tipo_cuenta" class="form-control" required>
                                            <option {{isset($dataEmpleados) && $dataEmpleados->account_type =='Ahorros' ? 'selected': ''}} value="Ahorros">Ahorros</option>
                                            <option {{isset($dataEmpleados) && $dataEmpleados->account_type =='Corriente' ? 'selected': ''}}  value="Corriente">Corriente</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">Número</span>
                                        <input type="number" min="0" class="form-control" id="numero_centa" name="numero_cuenta"
                                            value="{{isset($dataEmpleados) ? $dataEmpleados->account_number : ''}}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="padding: 0px 20px;">
                        <textarea required readonly class="ckeditor" name="body_contrato"  id="body_contrato" rows="10" cols="180">
                            @if($dataContratacion !== null)
                                {!! !empty($dataContratacion->cuerpo_contrato) ? $dataContratacion->cuerpo_contrato : ''!!}
                            @else
                                {!! !empty($dataContrato->cuerpo_contrato) ? $dataContrato->cuerpo_contrato : ''!!}
                            @endif
                        </textarea>
                    </div>
                </div>
                <button type="button" id="btn_contrataciones" class="btn btn-block btn-success btn-lg"
                        @if($dataContratacion === null) onclick="store_contratacion()" @else onclick="update_contratacion()" @endif>
                    <i id="ico" class="fa fa-floppy-o" aria-hidden="true"></i>
                    Guardar
                </button>
            </div>
        </div>
    </form>
@endsection

@section('custom_page_js')
    @include('layouts.views.contrataciones.script')
@endsection
