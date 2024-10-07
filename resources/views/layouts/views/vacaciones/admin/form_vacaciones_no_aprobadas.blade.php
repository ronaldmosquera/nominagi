<form id="form_comentario_no_aprobadas" name="form_comentario_no_aprobadas">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <div class="col-md-12" >
                <h3 class="box-title">Motivo de la no aprobación de vacaciones</h3>
            </div>
            <div class="col-xs-12" style="margin-top: 15px;">
                <input type="hidden" id="idVacaciones" value="">
                <div class=" ">
                    <textarea id="comentario" name="comentario" rows="3" placeholder="Exposición de motivos por el cual las vacaciones no fueron aprobadas"
                              style="width: 100%;" required>{{!empty($idVacaciones->comentarios) ? $idVacaciones->comentarios : ''}}</textarea>
                </div>
            </div>
            <button type="button" class="btn btn-info pull-right" id="btn_store_vacaciones_no_aprobadas"  style="margin-right: 15px;"
                    onclick="store_comentario_vacaciones_no_aprobadas('{{$idVacaciones->id_vacaciones}}','{{$idVacaciones->id_empleado}}')">
                <i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i>
                Guardar
            </button>
        </div>

    </div>
</form>