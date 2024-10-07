<form id="form_cargo" name="form_cargo">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <input type="hidden" id="id_cargo" value="{!! isset($dataCargo->id_cargo) ? $dataCargo->id_cargo : '' !!}">
        <div class="box-header with-border">
            <h3 class="box-title">Ingrese un nuevo cargo</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group ">
                        <span class="input-group-addon" style="background: #D9D9D9;">Nombre</span>
                        <input type="text" name="cargo" id="cargo" class="form-control" required minlength="3" value="{!! isset($dataCargo->nombre) ? $dataCargo->nombre : '' !!}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group ">
                        <span class="input-group-addon" style="background: #D9D9D9;">Descripción</span>
                        <input type="text" name="descripcion" id="descripcion" class="form-control" required
                               minlength="3" value="{!! isset($dataCargo->descripcion) ? $dataCargo->descripcion : '' !!}">
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: 20px">
                <div class="col-md-6">
                    <div class="input-group sueldo_sectorial">
                        <span class="input-group-addon" style="background: #D9D9D9;">Mínimo sectorial</span>
                        <input type="number" name="sueldo_minimo_sectorial" id="sueldo_minimo_sectorial" class="form-control" {{--onkeyup="salario_minimo()"--}}
                               required min="1"  value="{!! isset($dataCargo->sueldo_minimo_sectorial) ? $dataCargo->sueldo_minimo_sectorial : '' !!}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group sueldo_sectorial">
                        <span class="input-group-addon" style="background: #D9D9D9;">¿Cargo de confianza?</span>
                        <select  class="form-control" id="cargo_confianza" name="cargo_confianza" >
                            <option value="0" {!! isset($dataCargo->cargo_confianza) ? (!$dataCargo->cargo_confianza ? 'selected' : '') : '' !!}>No</option>
                            <option value="1" {!! isset($dataCargo->cargo_confianza) ? ($dataCargo->cargo_confianza ? 'selected' : '') : '' !!}>Si</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" style="padding-top: 30px">
                    <button type="button" class="btn btn-info pull-right" id="btn_submit" onclick="store_cargo()"><i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</form>