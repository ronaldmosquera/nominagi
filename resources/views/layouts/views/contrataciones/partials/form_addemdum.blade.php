<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#contratacion" data-toggle="tab">
                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                Ver detalles de la contratación
            </a>
        </li>
        <li>
            <a href="#crear" data-toggle="tab">
                <i class="fa fa-plus" aria-hidden="true"></i>
                Crear addendum
            </a>
        </li>
        <li>
            <a href="#ver" data-toggle="tab">
                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                Ver addendum
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="contratacion">
            <div class="row">
                <form id="form_contratacion" name="form_contratacion">
                    <input type="hidden" id="id_detalle_contratacion" name="id_detalle_contratacion" value="{{$dataContratacion->id_detalle_contrataciones}}">
                    <input type="hidden" id="party_id" value="{{$dataContratacion->id_empleado}}">
                    <input type="hidden" id="payment_method_id" value="{{isset($datosBancarios) ? $datosBancarios->payment_method_id : ''}}">
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">Empleado</span>
                                <input type="text" class="form-control" id="contratacion" name="contratacion" value="{{getNombreEmpleado($dataContratacion->id_contrataciones)->first_name." ".getNombreEmpleado($dataContratacion->id_contrataciones)->last_name}}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">Contratación</span>
                                <input type="text" class="form-control" id="contratacion" name="contratacion" value="{{$dataContratacion->nombre_contrato}}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">Salario</span>
                                <input type="text" class="form-control" id="salario" name="salario" value="{{$dataContratacion->salario}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">Expedición</span>
                                <input type="date" class="form-control" id="fecha" name="fecha" value="{{$dataContratacion->fecha_expedicion_contrato}}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">Cargo</span>
                                <input type="text" class="form-control" id="cargo" name="cargo" value="{{$dataContratacion->nombre_cargo}}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">Horas</span>
                                <input type="text" class="form-control" id="horas_laborales" name="horas_laborales" value="{{$dataContratacion->horas_jornada_laboral}}">
                            </div>
                        </div>
                    </div>
                    @if($dataContratacion->relacion_dependencia)
                        <div class="">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">Decimo 3ero</span>
                                        <select id="decimo_tercero"  name="decimo_tercero" class="form-control" required>
                                            <option disabled selected>Seleccione</option>
                                            <option value="1" {{$dataContratacion->decimo_tercero == true ? "selected" : "" }}> Mensualizado</option>
                                            <option value="0" {{$dataContratacion->decimo_tercero == false ? "selected" : "" }}> Anualizado</option>
                                        </select>
                                        {{--<input type="text" class="form-control" id="decimo_tercero" name="decimo_tercero" value="{{$dataContratacion->decimo_tercero == true ? "Mensualizado" : "Anualizado" }}" disabled>---}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">Decimo 4to</span>
                                        <select id="decimo_cuarto"  name="decimo_cuarto" class="form-control" required>
                                            <option disabled selected>Seleccione</option>
                                            <option value="1" {{$dataContratacion->decimo_cuarto == true ? "selected" : "" }}> Mensualizado</option>
                                            <option value="0" {{$dataContratacion->decimo_cuarto == false ? "selected" : "" }}> Anualizado</option>
                                        </select>
                                        {{--<input type="text" class="form-control" id="decimo_cuarto" name="decimo_cuarto" value="{{$dataContratacion->decimo_cuarto == true ? "Mensualizado" : "Anualizado" }}" disabled>--}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">Fondo reserva</span>
                                        <select id="fondo_reserva"  name="fondo_reserva" class="form-control" required>
                                            <option disabled selected>Seleccione</option>
                                            <option value="1" {{$dataContratacion->fondo_reserva == true ? "selected" : "" }}> Mensualizado</option>
                                            <option value="0" {{$dataContratacion->fondo_reserva == false ? "selected" : "" }}> Anualizado</option>
                                        </select>
                                        {{--<input type="text" class="form-control" id="fondo_reserva" name="fondo_reserva" value="{{$dataContratacion->fondo_reserva == true ? "Mensualizado" : "Anualizado" }}" disabled>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="">

                            <div class="col-md-6" >
                                <div class="form-group">
                                    <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                <i class="fa fa-file-text-o" aria-hidden="true"></i> DOCUMENTO
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

                            <div class="col-md-6" >
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
                            <div class="col-md-6" >
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

                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">Iva %</span>
                                        <select class="form-control" id="iva" name="iva" required>
                                            @foreach($iva as $i)
                                                <option {{$dataContratacion->iva == $i->valor ? "selected" : ""}} value="{{$i->valor}}">{{$i->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">Retención iva %</span>
                                        <input type="text" class="form-control" id="retencion_iva" name="retencion_iva" value="{{$dataContratacion->retencion_iva}}" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">Retención renta %</span>
                                        <input type="text" class="form-control" id="retencion_renta" name="retencion_renta" value="{{$dataContratacion->retencion_renta}}" >
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif
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
                                                <option
                                                    {{isset($datosBancarios) ? ($item->enum_id == $datosBancarios->codigo_banco ? 'selected' : '') : ''}}
                                                    value="{{$item->enum_id}}">{{$item->description}}</option>
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
                                            <option {{isset($datosBancarios) ? ($datosBancarios->account_type == 'Ahorros' ? 'selected' : '') : ''}} value="Ahorros">Ahorros</option>
                                            <option {{isset($datosBancarios) ? ($datosBancarios->account_type == 'Corriente' ? 'selected' : '') : ''}} value="Corriente">Corriente</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">Número</span>
                                        <input type="number" min="0" class="form-control" id="numero_cuenta" name="numero_cuenta"
                                            value="{{isset($datosBancarios) ? $datosBancarios->account_number : ''}}"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        <button type="button" id="btn_store_contrataciones" class="btn btn-success" onclick="update_detalle_contratacion()">
                            <i id="ico" class="fa fa-floppy-o" aria-hidden="true"></i>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="tab-pane" id="crear">
            <form id="addendum_contratacion" name="addendum_contratacion">
                <input type="hidden" id="id_detalle_contratacion" value="{{$dataContratacion->id_detalle_contrataciones}}">
                <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-group salario">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> <i class="fa fa-money" aria-hidden="true"></i> Salario $</span>
                                        <input type="number" min="" class="form-control" id="salario" name="salario" onkeyup="converitr_letras('{{$dataContratacion->salario}}',this.value)"
                                            required placeholder="Ej: 400.00" value="{{$dataContratacion->salario}}" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"  style="background: #d9d9d9;"> <i class="fa fa-clock-o"></i> Horas laborales</span>
                                        <input type="number" class="form-control" required value="{{$dataContratacion->horas_jornada_laboral}}" id="horas" name="horas" min="1" onkeypress="return filterFloat(event,this)" style="border-radius: 0px">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"><i class="fa fa-user-circle-o" aria-hidden="true"></i> Cargo </span>
                                        <select class="form-control" id="id_cargo" name="id_cargo" required>
                                            <option disabled selected>Seleccione</option>
                                            @foreach($dataCargos as $cargo)
                                                <option {{$dataContratacion->id_cargo == $cargo->id_cargo ? "selected='selected'" : ""}} value="{{$cargo->id_cargo}}">{{$cargo->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> <i class="fa fa-money" aria-hidden="true"></i> Salario en letras</span>
                                        <input type="text"  class="form-control" id="letras" name="letras" readonly="true"
                                               required placeholder="Ej: 400.00"  onkeypress="return filterFloat(event,this)">
                                    </div>
                                </div>
                            </div>
                            <div class="sin_relacion_dependencia"></div>
                            <div class="col-md-12 cuerpo">
                                <div class="form-group">
                                    <label>Tags para agregar información personalizada:</label> <br />
                                    <ul>
                                        <li>
                                            <label>Datos Empresa:</label> [NOMBRE_EMPRESA], [ID_EMPRESA], [DIREC_EMPRESA]
                                        </li>
                                        <li>
                                            <label>Datos Representante de la empresa: </label> [NOMBRE_REP_EMPRESA], [ID_REP_EMPRESA]
                                        </li>
                                        <li>
                                            <label>Datos empleado: </label> [NOMBRE_EMPLEADO], [ID_EMPLEADO], [NACIONALIDAD], [DIREC_EMPLEADO], [CARGO_EMPLEADO], [SALARIO_EMPLEADO], [HORAS_TRABAJO], [SALARIO_LETRAS]
                                        </li>
                                        <li>
                                            <label>Datos de fecha: </label>  [D_ACTUAL], [M_ACTUAL], [A_ACTUAL]
                                        </li>
                                        <li>
                                            <label>Salto de página: </label>  [SALTO_DE_PAGINA]
                                        </li>
                                    </ul>
                                </div>
                                <div class="form-group">
                                    <label> Cuerpo del addendum</label>
                                    <textarea class="ckeditor form-control" name="cuerpo_adendum"  id="cuerpo_adendum"
                                              required rows="10" cols="180"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="button" class="btn btn-default" id="btnclose" data-dismiss="modal">
                                    <i id="ico" class="fa fa-ban"></i> Cerrar
                                </button>
                                <button type="button" class="btn btn-success" onclick="store_addendum('{{$dataContratacion->salario}}','{{$dataContratacion->horas_jornada_laboral}}','{{$dataContratacion->id_cargo}}','{{$dataContratacion->iva}}','{{$dataContratacion->retencion_iva}}','{{$dataContratacion->retencion_renta}}')">
                                    <i class="fa fa-floppy-o" id="ico" aria-hidden="true"></i>
                                    Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="tab-pane" id="ver">
            <table class="table table-striped" id="tabla_tipo_contrato">
                @if(count($dataAddendum) >0)
                <tr>
                    <th class="text-center">Fecha creación</th>
                    <th class="text-center">Documento</th>
                </tr>

                    @foreach($dataAddendum as $key => $data)
                        <tr>
                            <td class="text-center">
                                {{$data->fecha}}
                            </td>
                            <td class="text-center">
                                <a target="_blank" class="btn btn-danger" href="{{asset('/contratos/'.$data->nombre_archivo)}}">
                                 <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <div class="alert alert-danger col-md-12" role="alert">
                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                        <span class="sr-only">Error:</span>
                        No posee addendums
                    </div>
                @endif
            </table>
        </div>
    </div>
    <!-- /.tab-content -->
</div>


<script>
    CKEDITOR.replace('cuerpo_adendum');
    converitr_letras();


    select_retencion()

    function select_retencion(){

        if($("#tipo_documento").val() == "INVOICE_HONORARIOS"){

            $("#tipo_impuesto_renta").attr('required',true)

        }else{

            $("#tipo_impuesto_renta").removeAttr('required')

        }

    }


    {{--@if($dataContratacion->relacion_dependencia == 0)
        campos_sin_relacion_dependencia('{{$dataContratacion->id_detalle_contrataciones}}');
    @endif--}}
</script>
