<section class="" style="padding: 0">
    <form id="form_feriados" name="form_feriados">
    <div class="box box-info">
        <div class="box-header" >
            <div class="col-md-4">
                <h3 class="box-title" style="margin-top: 10px">Configuración días feriados</h3>
            </div>
            <div class="col-md-8 text-right">
                <label>Fecha feriado</label>
                <input type="text" id="anno_mes_feriado" class="Date"
                       value="{{\Carbon\Carbon::parse(now()->toDateString())->format('m/Y')}}" onchange="buscar_fecha_feriado()"
                       name="horas" required style="border-radius: 0px;height: 34px;position:relative;top: 2px;border: 1px solid #d8d8d8;">
                <button type="button" class="btn btn-primary" title="Agregar feriado" onclick="add_feriado()">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-danger" title="Quitar feriado" onclick="delete_feriado()">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
        <div class="row">
            <div class="col-xs-12 input_feriados"></div>
        </div>
        <div class="row text-right">
            <div class="col-xs-12">
            <button type="button" class="btn btn-primary" onclick="guardar_feriados()">
                <i class="fa fa-floppy-o"></i> Guardar feriados
            </button>
            </div>
        </div>
    </form>
</section>
<script>

    //add_feriado();
    buscar_fecha_feriado();
    function add_feriado() {
        cant_inputs = $(".input_feriados div.col-md-4").length;
        $.ajax({
            method: 'GET',
            url: '{{route('vista.input_feriados')}}',
            data : { cant_inputs : cant_inputs },
            success: function (response) {
                $(".input_feriados").append(response);
            }
        });
    }

    function delete_feriado() {
        if($(".input_feriados div.col-md-4").length > 1)
            $(".input_feriados div.col-md-4:last-child").remove();
    }

    function buscar_fecha_feriado(){
        $.ajax({
            method: 'GET',
            url: '{{route('search.anno-mes-feriado')}}',
            data : { fecha_anno_mes_feriado : $("#anno_mes_feriado").val() },
            success: function (response) {
                $("div.input_feriados").empty();
                $(".input_feriados").append(response);
            }
        });
    }
    
    function guardar_feriados() {
        if($("#form_feriados").valid()){
            arr_datos = [];

            $.each($(".fecha_feriado"),function (i,j) { arr_datos.push({ fecha_feriado : j.value }) });

            $.ajax({
                method: 'POST',
                url: '{{route('store.fecha_feriado')}}',
                data : {
                    anno_mes_feriado : $("#anno_mes_feriado").val(),
                    arr_datos : arr_datos
                },
                success: function (response) {
                    open_modal_message('modal_body_message', 'modal_message', response.msg);
                }
            });
        }
    }
    $('.Date').datepicker({
        format: 'mm/yyyy',
        //endDate: '0d',
        //startDate: '0d',
        language: 'es-ES'
    });

</script>