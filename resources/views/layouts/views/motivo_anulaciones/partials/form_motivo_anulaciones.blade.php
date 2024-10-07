<form id="form_motivo_anulacion" name="form_motivo_anulacion">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <input type="hidden" id="id_motivo_anulacion" value="{!! isset($dataMotivoAnulacion->id_motivo_anulacion) ? $dataMotivoAnulacion->id_motivo_anulacion : '' !!}">
        <div class="box-header with-border">
            <h3 class="box-title">Ingrese un nuevo motivo de terminación de contrato</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group ">
                        <span class="input-group-addon" style="background: #D9D9D9;">Nombre</span>
                        <input type="text" name="nombre" id="nombre" class="form-control" required minlength="3"
                               value="{!! isset($dataMotivoAnulacion->nombre) ? $dataMotivoAnulacion->nombre : '' !!}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group ">
                        <span class="input-group-addon" style="background: #D9D9D9;">Descripción</span>
                        <input type="text" name="descripcion" id="descripcion" class="form-control"
                               value="{!! isset($dataMotivoAnulacion->descripcion) ? $dataMotivoAnulacion->descripcion : '' !!}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class='col-md-6' style='margin-top: 20px'><div class='input-group'>
                        <span class='input-group-addon' style='background: #D9D9D9;'>Calcula desahucio</span>
                        <select class='form-control' id='id_calculo_deshaucio' name='id_calculo_deshaucio'>
                            <option value=''>Seleccione</option>
                            <option value='1' {!! (isset($dataMotivoAnulacion->desahucio) && $dataMotivoAnulacion->desahucio == 1) ? "selected" : '' !!}>Sí</option>
                            <option value='0' {!! (isset($dataMotivoAnulacion->desahucio) && $dataMotivoAnulacion->desahucio == 0) ? "selected" : '' !!}>No</option>
                            </select>
                        </div>
                </div>
                <div class='col-md-6' style='margin-top: 20px'><div class='input-group'>
                        <span class='input-group-addon' style='background: #D9D9D9;'>Calcula despido intempestivo</span>
                        <select class='form-control' id='id_calculo_despido_intempestivo' name='id_calculo_despido_intempestivo'>
                            <option value=''>Seleccione</option>
                            <option value='1' {!! (isset($dataMotivoAnulacion->despido_intempestivo) && $dataMotivoAnulacion->despido_intempestivo == 1) ? "selected" : '' !!}>Sí</option>
                            <option value='0' {!! (isset($dataMotivoAnulacion->despido_intempestivo) && $dataMotivoAnulacion->despido_intempestivo == 0) ? "selected" : '' !!}>No</option>
                        </select>
                    </div>
                </div>
                <div class='col-md-6' style='margin-top: 20px'><div class='input-group'>
                        <span class='input-group-addon' style='background: #D9D9D9;'>¿Cálcula liquidación?</span>
                        <select class='form-control' id='calcula_liquidacion' name='calcula_liquidacion'>
                            <option value=''>Seleccione</option>
                            <option value='1' {!! (isset($dataMotivoAnulacion->calcula_liquidacion) && $dataMotivoAnulacion->calcula_liquidacion == 1) ? "selected" : '' !!}>Sí</option>
                            <option value='0' {!! (isset($dataMotivoAnulacion->calcula_liquidacion) && $dataMotivoAnulacion->calcula_liquidacion == 0) ? "selected" : '' !!}>No</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" style="padding-top: 10px">
                    <button type="button" class="btn btn-info pull-right" onclick="store_motivo_anulacion()"><i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</form>