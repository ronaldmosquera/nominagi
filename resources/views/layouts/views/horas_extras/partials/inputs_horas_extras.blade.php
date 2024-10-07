<div class="row" id="horas_{{$id}}" style="padding: 10px 0;">
    <div class="col-md-6">
        <div class="" data-toggle="tooltip" title="Fecha solicitud">
            <label>Fecha solicitud</label>
            <input type="text" name="fecha_solicitud_{{$id}}" id="fecha_solicitud_{{$id}}"
                   value="{{isset($dataHoraExtra->fecha_solicitud)? Carbon\Carbon::parse($dataHoraExtra->fecha_solicitud)->format('Y/m/d') : ''}}" class="form-control Date"
                   onchange="obtener_horario('{{session('dataUsuario')['id_empleado']}}')"
                   autocomplete="off" required>
        </div>
        <span id="error_fecha_{{$id}}"></span>
    </div>
    <div class="col-md-6">
        <div class="" data-toggle="tooltip" title="Cantidad de horas" >
            <label>Total horas</label>
            <input type="text" name="cantidad_horas_{{$id}}" id="cantidad_horas_{{$id}}"
                   value="{{isset($dataHoraExtra->cantidad_horas) ? $dataHoraExtra->cantidad_horas : ''}}"
                   placeholder="Cantidad de horas" class="form-control" readonly>
        </div>
    </div>
</div>
<div class="row inputs_horas">
    <div class="col-md-3">
        <div class="input-group" data-toggle="tooltip" title="Desde">
            <label>Desde</label>
            <input type="text" name="hora_desde_{{$id}}" onblur="total_horas_desde_entrada({{$id}})"
                   value="{{(!empty($horas) && $horas->desde >= $dataHoraExtra->hasta) ? (isset($dataHoraExtra->desde) ? $dataHoraExtra->desde : '') : ""}}" id="hora_desde_{{$id}}" class="form-control hours">
        </div>
        <span id="error_desde_{{$id}}"></span>
    </div>
    <div class="col-md-3">
        <div class="input-group" data-toggle="tooltip" title="Llegada">
            <label>Hora llegada</label>
            <input type="text" name="hora_llegada_{{$id}}"
                   value="{{(!empty($horas) && $horas->desde >= $dataHoraExtra->hasta) ? (isset($dataHoraExtra->hasta)? $dataHoraExtra->hasta : '') : "" }}" id="hora_llegada_{{$id}}" class="form-control" required readonly>
        </div>
    </div>
    <div class="col-md-3">
        <div class="input-group" data-toggle="tooltip" title="Salida">
            <label>Hora salida</label>
            <input type="text" name="hora_salida_{{$id}}" value="{{(!empty($horas) && $horas->desde <= $dataHoraExtra->hasta) ? (isset($dataHoraExtra->desde) ? $dataHoraExtra->desde : '') : ""}}"
                   id="hora_salida_{{$id}}" class="form-control" required readonly>
        </div>
    </div>
    <div class="col-md-3">
        <div class="input-group"  data-toggle="tooltip" title="Hasta">
            <label>Hasta</label>
            <input type="text" name="hora_hasta_{{$id}}" onblur="total_horas_desde_salida({{$id}})"
                 value="{{(!empty($horas) && $horas->desde <= $dataHoraExtra->hasta) ? (isset($dataHoraExtra->hasta) ? $dataHoraExtra->hasta : '') : "" }}"   id="hora_hasta_{{$id}}" class="form-control hours">
        </div>
        <span id="error_hasta_{{$id}}"></span>
    </div>
</div>
<div class="row input_comentario">
    <div class="" style="margin-top: 15px;">
        <div class="col-md-12">
            <label>Comentarios</label><br />
            <textarea id="comentarios_{{$id}}" name="comentarios_{{$id}}" rows="3" placeholder="Opcional (Motivo por el cual se solicitan las horas extras)"
                      style="width: 100%;" required>{{isset($dataHoraExtra->comentarios) ? $dataHoraExtra->comentarios : ''}}</textarea>
            <input type="hidden" id="id_hora_extra_{{$id}}" name="id_hora_extra_{{$id}}"
                   value="{{isset($dataHoraExtra->id_horas_extras)? $dataHoraExtra->id_horas_extras : ''}}">
        </div>
    </div>
</div>
</div>
<script>
    $('.Date').datepicker({
        format: 'dd/mm/yyyy',
        //endDate: '0d',
        //startDate: '0d',
        language: 'es-ES'
    });

    $('.hours').datetimepicker({
        format:"HH:mm",
        icons: {
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down"
        },
    });
    $('[data-toggle="tooltip"]').tooltip();

</script>
<style scope>
#comentarios_1-error {
    left: 18px!important;
    top: 88px!important;
}
</style>
