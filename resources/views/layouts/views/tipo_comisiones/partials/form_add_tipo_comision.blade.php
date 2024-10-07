<form id="form_tipo_comsion" name="form_tipo_comsion">
    <input type="hidden" id="id_tipo_comision" value="{{isset($tipoComision->id_tipo_comision) ? $tipoComision->id_tipo_comision : ''}}">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <h3 class="box-title">Ingrese el tipo de comisión</h3>
        </div>
        <div class="box-body">
            <div class="row"  style="margin-bottom: 10px">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                            Nombre
                        </span>
                        <input type="text" class="form-control" id="nombre" minlength="3" name="nombre"
                               value="{{isset($tipoComision->nombre) ? $tipoComision->nombre : ''}}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                            Monto estandar $
                        </span>
                        <input type="number" min="1" id="monto_estandar" class="form-control" name="monto_estandar"
                               value="{{isset($tipoComision->estandar) ? $tipoComision->estandar : '1'}}"   value="1" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                            ¿Calucla 10mo 3ero?
                        </span>
                        <select id="decimo_tercero" name="decimo_tercero" class="form-control" required>
                            <option disabled selected>Seleccione</option>
                            <option value="1" {{(isset($tipoComision->calculo_decimo_tercero) && $tipoComision->calculo_decimo_tercero == 1) ? "selected='selected'" : ''}}>Sí</option>
                            <option value="0" {{(isset($tipoComision->calculo_decimo_tercero) && $tipoComision->calculo_decimo_tercero == 0) ? "selected='selected'" : ''}}>No</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 20px">
                    <textarea class="form-control" id="descripcion" required
                              placeholder="Descripción de tipo de comisión" name="descripcion">{{isset($tipoComision->descripcion) ? trim($tipoComision->descripcion) : ''}}</textarea>
                </div>
            </div>
            <div class="text-rigth" style="padding-top: 10px">
                <button type="button" class="btn btn-info pull-right" id="btn_store_tipo_comsion" onclick="store_tipo_comision()">
                    <i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i> Guardar</button>
            </div>
        </div>
    </div>
</form>