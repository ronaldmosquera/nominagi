<script>

    obtener_horarios();

    function cargar_calendario(id_empleado) {

        $.ajax({
            method   : 'GET',
            url      : '{{route('horarios.show',0 )}}',
            data    :{
                id_empleado :  id_empleado
            },
            success: function (response) {
                $("#calendario").html(response);

            }
        });
    }

    function class_div(div) {
        $("#class").val("bg"+div.id.split('text')[1]);
    }
    
    function store_intervalo_horas() {

        if($('#form_intervalo_hora').valid()){
            iconAccion('ico','fa-paper-plane-o','fa-spinner fa-pulse fa-fw');
            $.ajax({
                method: 'POST',
                url: '{{route('store.horarios_intervalos')}}',
                data: {
                    entrada : $("#entrada").val(),
                    salida  : $("#salida").val(),
                    clase   : $("#class").val()
                },
                success: function (response) {
                    if (response.status == 1) {
                        iconAccion('ico', 'fa-spinner fa-pulse fa-fw', 'fa-check');
                        obtener_horarios();
                        $("#msg").html(response.msg);
                        $("#entrada,#salida").val('')
                    } else if(response.status == 0){
                        iconAccion('ico', 'fa-spinner fa-pulse fa-fw', 'fa-times');
                        $("#msg").html(response.msg);
                    }else if(response.status == 2){
                        open_modal_message('modal_body_message', 'modal_message', response.msg);
                    }
                    setTimeout(function () {
                        $("#msg").html('');
                    },2000);
                }
            });
        }
    }

    function obtener_horarios() {
        $("#external-events").hide();
        $.ajax({
            method: 'GET',
            url: '{{route('vista.obtener_horarios')}}',
            success: function (response) {
                $("#external-events").html(response);
                $("#external-events").show(500);
                init_events($('#external-events div.external-event'));
            }
        });
    }

    function eliminar_horario(div) {
        $.ajax({
            method: 'POST',
            url: '{{route('delete.horarios_intervalos')}}',
            data: {
                id : div.id
            },
            success: function (response) {
                if (response.status == 1) {
                    obtener_horarios();
                    $("#msg_delete").html(response.msg);
                } else{
                    $("#msg_delete").html(response.msg);
                }
                setTimeout(function () {
                    $("#msg_delete").html('');
                },2000);
            }
        });
    }

    function guardar_horarios() {

        var data = $("#calendar").fullCalendar('clientEvents');

        var arrData = [];
        var arrDataRange = [];

        for (var i = 0; i < data.length; i++) {

            if (data[i].end != null) {
                var inicio = data[i].start.format("YYYY-MM-DD");
                var fin = data[i].end.format("YYYY-MM-DD");

                var diferencia_dias = moment(fin).diff(moment(inicio), 'days');

                var fecha = moment(inicio).subtract(1, 'days').format('YYYY-MM-DD');

                for (var j = 1; j <= diferencia_dias; j++) {

                    fecha = moment(fecha).add(1, 'days').format('YYYY-MM-DD');
                    arrDataRange.push([
                        data[i].title.split(" ")[1],
                        data[i].title.split(" ")[3],
                        fecha,
                        $("#id_empleado").val(),
                        data[i].className[0]
                    ]);
                }

            } else {


                arrData.push([
                    data[i].title.split(" ")[1],
                    data[i].title.split(" ")[3],
                    data[i].start.format("YYYY-MM-DD"),
                    $("#id_empleado").val(),
                    data[i].className[0]
                ]);
            }
        }

        arrData = arrData.concat(arrDataRange);
        if (arrData.length < 1 || $("#id_empleado").val().length < 1) {
            open_modal_message('modal_body_message', 'modal_message', '<div class="alert alert-danger" role="alert" style="margin: 0"> Debe asignar días laborables un empleado </div>');
            return false;
        }

        iconAccion('ico_store', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');

        $.ajax({
            method: 'POST',
            url: '{{route('horarios.store')}}',
            data:
                {
                    arrData: JSON.stringify(arrData)
                },
            success: function (response) {
                if (response.status == 1) {
                    iconAccion('ico_store', 'fa-spinner fa-pulse fa-fw', 'fa-check');
                } else if (response.status == 0) {
                    iconAccion('ico_store', 'fa-spinner fa-pulse fa-fw', 'fa-times');
                }
                open_modal_message('modal_body_message', 'modal_message', response.msg);
                cargar_calendario(arrData[0][3]);
            }
        });
    }

    $('#entrada,#salida').datetimepicker({ format:"HH:mm" });

    setTimeout(function() {
        init_events($('#external-events div.external-event'));
    },1500);

    function configurar_feriado() {
        $.ajax({
            method: 'GET',
            url: '{{route('vista.configurar_feriados')}}',
            success: function (response) {
                open_modal_form('body_modal', 'modal-default', response, 'modal-lg');
            }
        });
    }


</script>