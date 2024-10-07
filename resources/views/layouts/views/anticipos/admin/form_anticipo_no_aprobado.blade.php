<form id="form_comentario_no_aprobado" name="form_comentario_no_aprobado">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <div class="col-md-6" >
                <h3 class="box-title">Motivo de la no aprobación del anticipo</h3>
            </div>
            <div class="col-xs-12" style="margin-top: 15px;">
                <div class=" ">
                    <textarea id="comentario" name="comentario" rows="3" placeholder="Exposición de motivos por el cual no se aprobó el anticipo"
                              style="width: 100%;" class="form-control" required>{{!empty($dataAnticipo->comentario) ? $dataAnticipo->comentario : ''}}</textarea>
                </div>
            </div>
            <button type="button" class="btn btn-info pull-right" id="btn_store_anticipo_no_aprobado"  style="margin-right: 15px;margin-top:10px"
                    onclick="store_comentario_anticipo_no_aprobado('{{$dataAnticipo->id_anticipo}}')">
                <i id="btn_ico" class="fa fa-floppy-o" aria-hidden="true" ></i>
                Guardar
            </button>
        </div>

    </div>
</form>