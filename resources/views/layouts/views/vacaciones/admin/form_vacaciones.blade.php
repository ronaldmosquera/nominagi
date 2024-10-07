<form id="form_vacaciones" name="form_vacaciones">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <div class="col-md-12">
                <h3 class="box-title">Ingrese las vacaciones a solicitar</h3>
            </div>
        </div>
        <div class="box-body">
            {{-- <div class="row">
                <div class="col-xs-4">
                    <div class="input-group" data-toggle="tooltip" title="Periodo desde">
                        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer">
                            Periodo desde
                        </span>
                        <input type="number" name="periodo_desde" id="periodo_desde" class="yearpicker form-control" autocomplete="off"
                                value="{{isset($dataVacaciones->periodo_desde) ? $dataVacaciones->periodo_desde : ''}}" required>
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="input-group" data-toggle="tooltip" title="Periodo hasta">
                        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer">
                            Periodo hasta
                        </span>
                        <input type="number" name="periodo_hasta" id="periodo_hasta" class="yearpicker form-control" autocomplete="off"
                                value="{{isset($dataVacaciones->periodo_hasta) ? $dataVacaciones->periodo_hasta : ''}}" required>
                    </div>
                </div>
            </div> --}}
            <div class="row" style="padding: 10px 0;">
                <div class="col-xs-4">
                    <div class="input-group" data-toggle="tooltip" title="Inicio de vacaciones">
                        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                        </span>
                        <input type="date" name="fecha_inicio"id="fecha_inicio" onchange="verificar_fechas_admin('{{$dataVacaciones->id_empleado}}')"
                               value="{{isset($dataVacaciones->fecha_inicio) ? $dataVacaciones->fecha_inicio : ''}}" class="form-control Date" required >
                        <input type="hidden" value="{{isset($dataVacaciones->id_vacaciones) ? $dataVacaciones->id_vacaciones : ''}}" id="id_vacacion">
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="input-group" data-toggle="tooltip" title="Fin de vacaciones">
                            <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                            </span>
                        <input type="date" name="fecha_fin" id="fecha_fin" onchange="verificar_fechas_admin('{{$dataVacaciones->id_empleado}}')"
                               value="{{isset($dataVacaciones->fecha_fin) ? $dataVacaciones->fecha_fin : ''}}" class="form-control Date" required >
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="input-group" data-toggle="tooltip" title="Cantidad de días">
                            <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                                <i class="fa fa-adjust" aria-hidden="true"></i>
                            </span>
                        <input type="text" name="cant_dias" id="cant_dias"
                               readonly value="{{isset($dataVacaciones->cantidad_dias)? $dataVacaciones->cantidad_dias : ''}}" class="form-control Date" >
                    </div>
                </div>
            </div>
            {{--<div class="row" >
                <div class="col-md-12" >
                    <input type="checkbox" id="atrasadas"> ¿Vacaciones atrasadas?
                </div>
            </div>--}}
            <div style="padding-top: 10px">
                <div class="col-md-11" style="padding: 0;">
                    <span id="msg" class="error"></span>
                </div>
                <div class="col-md-1 text-right" style="padding: 0;">

                    <button type="button" class="btn btn-info pull-right" id="btn_store_vacaciones" onclick="store_edit_vacaciones_admin()">
                        <i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    $('.yearpicker').datepicker({
        format: 'yyyy'
    });
    $('[data-toggle="tooltip"]').tooltip();
    $("#btn_store_vacaciones").attr('disabled',true);
</script>
<style>
    .input-periodo{
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
    }
</style>
