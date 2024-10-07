<form id="form_alcance_nomina" name="form_alcance_nomina">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <h3 class="box-title">Alcance de nómina a {{$empleado}}</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;">
                            Sueldo Base
                        </span>
                        <input type="number" class="form-control" name="sueldo" id="sueldo">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9" >
                            Horas Extras
                        </span>
                        <input type="number" min="0" class="form-control" name="he" id="he">
                    </div>
                </div>
                <div class="col-md-6" style='margin-top:10px'>
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9" >
                            Comisiones
                        </span>
                        <input type="number" min="0" class="form-control" name="comision" id="comision">
                    </div>
                </div>
                <div class="col-md-6" style='margin-top:10px'>
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9" >
                            Bonos
                        </span>
                        <input type="number" min="0" class="form-control" name="bono" id="bono">
                    </div>
                </div>
                @if($relacionDependencia)
                    <div class="col-md-6" style='margin-top:10px'>
                        <div class="input-group">
                            <span class="input-group-addon" style="background: #D9D9D9" >
                                10mo 3ero
                            </span>
                            <input type="number" min="0" class="form-control" name="10mo3ero" id="10mo3ero">
                        </div>
                    </div>
                    <div class="col-md-6" style='margin-top:10px'>
                        <div class="input-group">
                            <span class="input-group-addon" style="background: #D9D9D9" >
                                10mo 4to
                            </span>
                            <input type="number" min="0" class="form-control" name="10mo4to" id="10mo4to">
                        </div>
                    </div>
                    <div class="col-md-6" style='margin-top:10px'>
                        <div class="input-group">
                            <span class="input-group-addon" style="background: #D9D9D9" >
                                Fondo de reserva
                            </span>
                            <input type="number" min="0" class="form-control" name="fondo_reserva" id="fondo_reserva">
                        </div>
                    </div>
                @endif
                <div class="col-md-12" style='margin-top:10px'>
                    <textarea class="form-control" id="comentario" required id="comentario" name="comentario"
                                placeholder="Motivo por el cual se asigna el alcance"></textarea>
                </div>
            </div>
            <div  style="padding-top: 10px" class="col-md-12 text-center">
                <button type="button" class="btn btn-info"
                    id="btn_nivelar_nomina" onclick="generar_alcance_nomina({{$idNomina}})">
                    <i id="ico" class="fa fa-floppy-o"></i>
                    Guardar
                </button>
            </div>
        </div>
    </div>
</form>
