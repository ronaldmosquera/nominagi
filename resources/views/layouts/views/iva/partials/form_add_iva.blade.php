<form id="form_add_iva" name="form_add_iva" novalidate>
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <h3 class="box-title">Agregar iva</h3>
        </div>
        <div class="box-body">
            <input type="hidden" id="id_iva" value="{{isset($iva->id_iva) ? $iva->id_iva : ""}}">
            <div class="row">
                <div class="col-md-6">
                    <div class="">
                        <label> Nombre</label>
                        <input type="text" name="nombre" placeholder="Ej: 12%" id="nombre" class="form-control" value="{{isset($iva->nombre) ? $iva->nombre : ""}}" required >
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="">
                        <label> Valor</label>
                        <input type="number" name="valor" id="valor" placeholder="Ej: 12" class="form-control" value="{{isset($iva->valor) ? $iva->valor : ""}}" required >
                    </div>
                </div>
                <div class="col-md-12" style="padding-top: 10px">
                    <button type="button" class="btn btn-success pull-right" id="btn_store_iva" onclick="store_iva()">
                        <i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>