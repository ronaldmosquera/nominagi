<form id="form_cash_management_liquidaciones" name="form_cash_management_liquidaciones">
    <div class="box box-info" style="box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <h3 class="box-title">Gestión de pago de liquidaciones</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;">
                            Referencia bancaria
                        </span>
                        <input class="form-control" name="referencia_bancaria" id="referencia_bancaria" required>
                    </div>
                </div>
            </div>
            <div style="padding-top: 25px" class="col-md-12 text-center">
                <button type="button" class="btn btn-info"
                    id="btn_nivelar_nomina" onclick="store_referencia_bancaria_liquidacion()">
                    <i id="ico" class="fa fa-floppy-o"></i>
                    Guardar referencia
                </button>
                <button type="button" class="btn btn-success"
                    id="btn_nivelar_nomina" onclick="download_cash_management_liquidacion()">
                    <i id="ico" class="fa fa-cloud-download"></i>
                    Descargar archivo cash
                </button>
            </div>
        </div>
    </div>
</form>
