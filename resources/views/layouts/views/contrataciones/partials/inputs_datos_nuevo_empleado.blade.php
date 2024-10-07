@if(isset($vista) &&  $vista =='editar_empleado')
    <form id="form_edit_empleado" name="form_edit_empleado" novalidate="novalidate">
        <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
            <input type="hidden" id="id_empleado" value="{!! isset($dataEmpleados->party_id) ? $dataEmpleados->party_id : '' !!}">
                <div class="box-header with-border">
                    <h3 class="box-title">Edite los datos del empleado</h3>
                </div>
            <div class="box-body">
@endif
                <div class="row">
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
                <div class="row">
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
                                <select class="form-control" id="tipo_identificacion" name="tipo_identificacion" required>
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
                                <input type="text" class="form-control" id="identificacion"
                                       value="{!! isset($dataEmpleados->id_value) ? $dataEmpleados->id_value : '' !!}" name="identificacion" minlength="5" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
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
                                <input type="text" class="form-control" id="telefono"
                                       value="{!! isset($dataEmpleados->party_id) ? (isset(getTelecomNumber($dataEmpleados->party_id)->contact_number) ? getTelecomNumber($dataEmpleados->party_id)->contact_number : '') : "" !!}" name="telefono" minlength="7" required>
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
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> Provincia </span>
                                <select class="form-control" id="id_provincia" name="id_provincia" required>
                                    <option disabled selected>Seleccione</option>
                                    @foreach($dataProvinicias as $provinicia)
                                        <option
                                                {!!isset($dataEmpleados->party_id) ? ( isset(getPostalAddres($dataEmpleados->party_id)->state_province_geo_id)
                                                ? getPostalAddres($dataEmpleados->party_id)->state_province_geo_id == $provinicia->geo_id
                                                    ? "selected='selected'" : ''
                                                : '') : "" !!}
                                                value="{{$provinicia->geo_id}}">{{$provinicia->geo_name}}</option>
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
                                       value="{!! isset($dataEmpleados->party_id) ? (isset(getPostalAddres($dataEmpleados->party_id)->address1) ? getPostalAddres($dataEmpleados->party_id)->address1 : '') : "" !!}" name="C_V" minlength="2" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">  Ciudad </span>
                                <input type="text" class="form-control" id="ciudad" name="ciudad"
                                       value="{!! isset($dataEmpleados->party_id) ? (isset(getPostalAddres($dataEmpleados->party_id)->city) ? getPostalAddres($dataEmpleados->party_id)->city : '') : "" !!}" minlength="3" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> Nombres Contacto </span>
                                <input type="text" class="form-control" id="nombre_contacto"  name="nombre_contacto"
                                       value="{!! isset($dataEmpleados->first_name_contact) ? $dataEmpleados->first_name_contact : '' !!}"  minlength="3" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">  Apellidos Contacto </span>
                                <input type="text" class="form-control" id="apellido_contacto"  name="apellido_contacto"
                                       value="{!! isset($dataEmpleados->last_name_contact) ? $dataEmpleados->last_name_contact : '' !!}"  minlength="3" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">  Tlf Contacto (Movíl) </span>
                                <input type="tel" class="form-control" id="telefono_contacto"
                                       value="{!! isset($dataEmpleados->party_id) ? (isset(getTelecomNumber($dataEmpleados->party_id_contact)->contact_number) ? getTelecomNumber($dataEmpleados->party_id_contact)->contact_number : '') : "" !!}"
                                        name="telefono_contacto" minlength="7" >
                            </div>
                        </div>
                    </div>
                </div>
@if(isset($vista) &&  $vista =='editar_empleado')
              <div class="col-md-12" style="padding: 10px 0 0 0">
                <button type="button" class="btn btn-info pull-right" id="btn_store_contrato"
                        onclick="store_datos_empleado('{!! isset($dataEmpleados->party_id) ? (isset($dataEmpleados->party_id) ? $dataEmpleados->party_id : '') : "" !!}')">
                    <i id="ico" class="fa fa-floppy-o"></i>
                    Guardar
                </button>
               </div>
            </div>
        </div>
    </form>
@endif