<form id="form_respuesta_comentario" name="form_respuesta_comentario">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <div class="col-md-12" >
                <h3 class="box-title">Responder comentario (No aprobar horas extra)</h3>
            </div>
            <div class="col-xs-12" style="margin-top: 15px;">
                <input type="hidden" id="idHoraExtra" value="{{$idHoraExtra}}">
                <div class=" ">
                    <textarea id="comentario" name="comentario" rows="3" placeholder="Respuesta al comentario del empleado"
                              style="width: 100%;">
                    </textarea>
                </div>
            </div>
            <button type="button" class="btn btn-info pull-right" id="btn_add_horas_extras"  style="margin-right: 15px;"
                    onclick="store_respuesta_comentario()">
                <i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i>
                Guardar
            </button>
        </div>

    </div>
</form>