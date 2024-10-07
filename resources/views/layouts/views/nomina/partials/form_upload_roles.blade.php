<form id="form_add_imagenes_roles"   enctype="multipart/form-data" novalidate="novalidate">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <h3 class="box-title">Suba la(s) imagen(es) correspondiente(s)</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <label>Fecha nómina</label>
                    <input type="date" class="form-control" value="{{\Carbon\Carbon::now()->subMonth(1)->format('Y-m-05')}}"
                           name="fecha_nomina" id="fecha_nomina" required>
                </div>
                <div class="col-md-3">
                    <label>Tipo</label>
                    <select id="tipo" name="tipo" class="form-control" required>
                        <option value="1">Nómina</option>
                        <option value="2">Liquidación</option>
                        <option value="3">Alcance de nómina</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label>Imágenes</label>
                    <input type="file" class="form-control" name="file[]" id="file" multiple required
                           accept="image/.jpg,.png,.JPG,.PNG">
                </div>
            </div>
            <div  style="padding-top: 10px">
                <button type="button" class="btn btn-info pull-right" id="btn_upload_contrataciones_firmadas" onclick="upload_rol_firmado()">
                    <i id="ico" class="fa fa-floppy-o"></i>
                    Guardar</button>
            </div>
        </div>
    </div>
</form>
