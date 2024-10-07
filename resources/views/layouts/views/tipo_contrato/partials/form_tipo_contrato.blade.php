<form id="form_add_tipo_contrato" novalidate="novalidate">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
    <input type="hidden" id="id_tipo_contrato" value="{!! !empty($dataTipoContrato) ? $dataTipoContrato->id_tipo_contrato : ''!!}">
        <div class="box-header with-border">
            <h3 class="box-title">Ingrese un nuevo tipo de contrato</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                                Nombre
                            </span>
                        <input type="text" class="form-control" name="nombre" id="nombre"  placeholder="Nombre" required minlength="3"
                            value="{!! !empty($dataTipoContrato) ? $dataTipoContrato->nombre : ''!!}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                            Descripción
                        </span>
                        <input type="text" class="form-control" placeholder="Descripción" name="descripcion" id="descripcion"
                               value="{!! !empty($dataTipoContrato) ? $dataTipoContrato->descripcion : ''!!}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                            Tipo
                        </span>
                    <select class="form-control" id="id_tipo_contrato_descripcion" onchange="configuraciones()" required>
                        <option selected disabled > Seleccion </option>
                        @foreach($datTipocontratoDescripcion as $tipocontratoDescripcion)
                            @php
                                $selected='';
                                if(isset($dataTipoContrato->id_tipo_contrato_descripcion)){
                                    if($tipocontratoDescripcion->id_tipo_contrato_descripcion == $dataTipoContrato->id_tipo_contrato_descripcion){
                                        $selected= "selected='selected'";
                                    }
                                }
                            @endphp
                            <option {{$selected}} value="{{$tipocontratoDescripcion->id_tipo_contrato_descripcion}}" > {{$tipocontratoDescripcion->descripcion_tipo_contrato}} </option>
                        @endforeach
                    </select>
                    </div>
                </div>
            </div>
            <div class="row {{isset($dataTipoContrato->id_tipo_contrato_descripcion) && $dataTipoContrato->id_tipo_contrato_descripcion == 1 ? "hide" : ""}}" id="config1" style="margin-top: 20px">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                            Relación de dependencia
                        </span>
                        <select class="form-control" id="relacion_dependencia"  name="relacion_dependencia" required>
                            <option selected disabled > Seleccione </option>
                            <option value="1" {{ (!empty($dataTipoContrato->relacion_dependencia) && $dataTipoContrato->relacion_dependencia == 1) ? "selected='selected'" : ''}}>Bajo relación de dependencia</option>
                            <option value="0" {{ (!isset($dataTipoContrato->relacion_dependencia) || $dataTipoContrato->relacion_dependencia == '')? "selected='selected'" : ''}}>Sin relación de dependencia</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                            Horas extras
                        </span>
                        <select class="form-control" id="horas_extras" >
                            <option selected disabled > Seleccione </option>
                            <option value="1" {{ (!empty($dataTipoContrato->horas_extras) && $dataTipoContrato->horas_extras == 1) ? "selected='selected'" : ''}}>Posee horas extras </option>
                            <option value="0" {{ (!isset($dataTipoContrato->horas_extras) || $dataTipoContrato->horas_extras == '')? "selected='selected'" : ''}}>No posee horas extras</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row {{isset($dataTipoContrato->id_tipo_contrato_descripcion) && $dataTipoContrato->id_tipo_contrato_descripcion == 1 ? "hide" : ""}}" id="config2" style="margin-top: 20px">
                <div id="div_plazo">
                    <div class="col-md-6 div_input_plazo">
                        <div class="input-group">
                            <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                                ¿Plazo (Días)?
                            </span>
                            <select class="form-control" id="caducidad" name="caducidad" required>
                                <option selected disabled > Seleccione </option>
                                <option value="1" {{ (!empty($dataTipoContrato->caducidad) && $dataTipoContrato->caducidad == 1) ? "selected='selected'" : ''}}>Posee días de caducidad</option>
                                <option value="0" {{ (!isset($dataTipoContrato->caducidad) || $dataTipoContrato->caducidad == '')? "selected='selected'" : ''}}>No posee días de caducidad</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 div_input_plazo">
                        <div class="input-group">
                            <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                                ¿Validación sueldo sectorial?
                            </span>
                            <select class="form-control" id="sueldo_sectorial" name="sueldo_sectorial" required>
                                <option selected disabled > Seleccione </option>
                                <option value="1" {{ (!empty($dataTipoContrato->sueldo_sectorial) && $dataTipoContrato->sueldo_sectorial == 1) ? "selected='selected'" : ''}}>Sí</option>
                                <option value="0" {{ (!isset($dataTipoContrato->sueldo_sectorial) || $dataTipoContrato->sueldo_sectorial == '')? "selected='selected'" : ''}}>No</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div  style="padding-top: 10px">
                <button type="button" class="btn btn-info pull-right" id="btn_store_contrato" onclick="store_tipo_contrato()">
                    <i id="ico" class="fa fa-floppy-o"></i>
                    Guardar</button>
            </div>
        </div>
    </div>
</form>