<script>
    function add_horas_extras(id_horas_extras) {
        $.ajax({
            method   : 'GET',
            url      : '{{route('horas-extras.create')}}',
            data    :{
                id_horas_extras : id_horas_extras
            },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-lg');
            }
        });
    }

    function delete_horas_extras(id_horas_extras) {
        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea eliminar la hora extra?",
            callback: function (result) {
                if (result) {
                    $.ajax({
                        type: 'POST',
                        url: '{{route('delete.hora-extra',0)}}',
                        data: {
                            id_horas_extras: id_horas_extras
                        },
                        success: function (response) {
                            open_modal_message('modal_body_message', 'modal_message', response);
                        }
                    });
                }
            }
        });
    }

    function store_horas_extras(id_empleado) {

        if($('#form_horas_extras').valid()){

            /*var cant_inputs = 1;//$("div#inputs_horas_extras div.row").length;
            var arrData = [];

            for(var i=1;i<=cant_inputs; i++){
                arrData.push([
                    $("#fecha_solicitud_"+i).val(), //0
                    $("#hora_desde_"+i).val(),      //1
                    $("#hora_hasta_"+i).val(),      //2
                    $("#cantidad_horas_"+i).val(),  //3
                    $("#comentarios_"+i).val(),     //4
                    $("#id_hora_extra_"+i).val(),   //5
                    id_empleado                     //6
                ]);
            }*/

            if($(".inputs_horas").hasClass('hide')){

                arrData=[
                    [
                        $("#fecha_solicitud_1").val(), //0
                        $("#hora_llegada_1").val(),    //1
                        $("#hora_salida_1").val(),     //2
                        $("#cantidad_horas_1").val()+":"+"00",  //3
                        $("#comentarios_1").val(),     //4
                        $("#id_hora_extra_1").val(),   //5
                        id_empleado                    //6
                    ]
                ];
            }else{

                arr_fecha = $("#fecha_solicitud_1").val().split("/");
                fecha= arr_fecha[1]+'/'+arr_fecha[0]+'/'+arr_fecha[2];
                fecha = new Date(fecha).getDay();
                console.log(fecha)
                if(fecha !== 6 && fecha !== 0){
                    if($("#hora_desde_1").val() == "" && $("#hora_hasta_1").val() == ""){
                        msg = "<div class='alert alert-danger' role='alert' style='margin-bottom: 10px'>"+
                            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> " +
                            "Debe seleccionar la hora en el campo Desde o Hasta"+
                            "</div>";
                        open_modal_message('modal_body_message','modal_message',msg);
                        return false;
                    }
                }

                arrData=[
                    [
                        $("#fecha_solicitud_1").val(), //0
                        "",                            //1
                        "",                            //2
                        $("#cantidad_horas_1").val(),  //3
                        $("#comentarios_1").val(),     //4
                        $("#id_hora_extra_1").val(),   //5
                        id_empleado                    //6
                    ]
                ];
                if(fecha !== 6 && fecha !== 0) {
                    if ($("#hora_desde_1").val() == "") {
                        arrData[0][1] = $("#hora_salida_1").val();
                        arrData[0][2] = $("#hora_hasta_1").val();
                    } else {
                        arrData[0][1] = $("#hora_desde_1").val();
                        arrData[0][2] = $("#hora_llegada_1").val();
                    }
                }else{
                    arrData[0][1] = $("#hora_llegada_1").val();
                    arrData[0][2] = $("#hora_salida_1").val();
                }

            }

            iconAccion('ico','fa-floppy-o','fa-spinner fa-pulse fa-fw');
            $.ajax({
                method : 'POST',
                url    : '{{route('horas-extras.store')}}',
                data   : {
                    arrData : arrData,
                },
                success:function (response) {

                    if(response.status == 1){
                        iconAccion('ico','fa-spinner fa-pulse fa-fw','fa-check');
                    }else{
                        iconAccion('ico','fa-spinner fa-pulse fa-fw','fa-times');
                    }
                    open_modal_message('modal_body_message','modal_message',response.msg);
                }
            });
        }
    }

    function add_inputs(id_empleado,id_hora_extra) {

        var cant_inputs = $("div#inputs_horas_extras div.row").length;
        $("#btn_add_inputs").attr('disabled',true);
        $.ajax({
            method   : 'GET',
            url      : '{{route('vista.add_inputs_horas_extra')}}',
            data    :{
                id_empleado  : id_empleado,
                cant_inputs  : cant_inputs+1,
                id_hora_extra: id_hora_extra
            },
            success: function (response) {
                $("#inputs_horas_extras").append(response);
                $("#btn_add_inputs").attr('disabled',false);
            }
        });
    }

    function delete_inputs() {
        var cant_inputs = $("div#inputs_horas_extras div.row").length;
        if(cant_inputs != 1){
            $("#horas_"+cant_inputs).remove();
        }
    }

    function total_horas_desde_entrada(row) {

        var desde = moment($("#hora_desde_"+row).val(),"HH:mm");
        var hasta = moment($("#hora_llegada_"+row).val(),"HH:mm");


        if(desde.isBefore(hasta) ){

            $("#error_desde_"+row).html("");

            /*var arr_hora_minutos_desde  = $("#hora_desde_"+row).val().split(":");
            var arr_hora_minutos_hasta = $("#hora_hasta_"+row).val().split(":");

            var horas = parseInt(arr_hora_minutos_hasta[0]) - parseInt(arr_hora_minutos_desde[0]);
            var minutos = parseInt(arr_hora_minutos_hasta[1]) + parseInt(arr_hora_minutos_desde[1]);*/

            var start = moment.duration($("#hora_desde_"+row).val(), "HH:mm"),
                end   = moment.duration($("#hora_llegada_"+row).val(), "HH:mm"),
                diff = end.subtract(start);

            var horas = diff.hours();
            var minutos = diff.minutes();

            if(minutos<10)
                minutos = "0"+minutos;
            $("#hora_hasta_1").val("");
            $("#cantidad_horas_"+row).val(horas+":"+minutos);
            $("#btn_add_horas_extras,#btn_store_horas_extras").attr('disabled',false);
            $("#error_desde_"+row).html("");
            $("#error_hasta_"+row).html("");
        }else{
            $("#hora_hasta_1").val("");
            $("#error_hasta_"+row).html("");
            $("#btn_store_horas_extras,#btn_store_horas_extras").attr('disabled',true);
            $("#btn_add_horas_extras").attr('disabled',true);
            $("#error_desde_"+row).html("<span class='error' id='error'>Debe elegir una hora menor a la hora de llegada</span>");

        }
    }

    function total_horas_desde_salida(row) {

        var desde = moment($("#hora_salida_"+row).val(),"HH:mm");
        var hasta = moment($("#hora_hasta_"+row).val(),"HH:mm");


        if(desde.isBefore(hasta) ){

            $("#error_hasta_"+row).html("");

            /*var arr_hora_minutos_desde  = $("#hora_desde_"+row).val().split(":");
            var arr_hora_minutos_hasta = $("#hora_hasta_"+row).val().split(":");

            var horas = parseInt(arr_hora_minutos_hasta[0]) - parseInt(arr_hora_minutos_desde[0]);
            var minutos = parseInt(arr_hora_minutos_hasta[1]) + parseInt(arr_hora_minutos_desde[1]);*/

            var start = moment.duration($("#hora_salida_"+row).val(), "HH:mm"),
                end   = moment.duration($("#hora_hasta_"+row).val(), "HH:mm"),
                diff = end.subtract(start);

            var horas = diff.hours();
            var minutos = diff.minutes();

            if(minutos<10)
                minutos = "0"+minutos;
            $("#hora_desde_"+row).val("");
            $("#cantidad_horas_"+row).val(horas+":"+minutos);
            $("#btn_add_horas_extras,#btn_store_horas_extras").attr('disabled',false);
            $("#error_hasta_"+row).html("");
            $("#error_desde_"+row).html("");
        }else{
            $("#hora_desde_"+row).val("");
            $("#error_desde_"+row).html("");
            $("#btn_add_horas_extras,#btn_store_horas_extras").attr('disabled',true);
            $("#error_hasta_"+row).html("<span class='error' id='error'>Debe elegir una hora mayor a la hora de salida</span>");

        }
    }

    function save_success_horas_extras(estado) {

        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea aprobar las horas extras? seleccionadas?",
            callback: function (result) {
                if (result) {
                    arrIdHorasExtras = [];
                    $.each($('input:checkbox[name=checkHoraExtra]:checked'), function (i, j) {
                        arrIdHorasExtras.push(j.value);
                    });

                    $.ajax({
                        method: 'POST',
                        url: '{{route('vista.success_horas_extras_admin')}}',
                        data: {
                            arrIdHorasExtras,
                            estado
                        },
                        success: function (response) {
                            if (response.status == 1) {
                                iconAccion('ico', 'fa-spinner fa-pulse fa-fw', 'fa-check');
                            } else {
                                iconAccion('ico', 'fa-spinner fa-pulse fa-fw', 'fa-times');
                            }
                            open_modal_message('modal_body_message', 'modal_message', response.msg);
                        }
                    });
                }
            }
        });
    }

    function responder(id_hora_extra) {

        $.ajax({
            method   : 'GET',
            url      : '{{route('vista.responder_comentario_horas_extras')}}',
            data    :{
                id_hora_extra  : id_hora_extra,
            },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-xs');
            }
        });

    }

    function store_respuesta_comentario() {

        iconAccion('ico','fa-floppy-o','fa-spinner fa-pulse fa-fw');
        $.ajax({
            method : 'POST',
            url    : '{{route('respuesta.store_respuesta_comentario')}}',
            data   : {
                idHoraExtra : $("#idHoraExtra").val(),
                comentario  : $("#comentario").val()
            },
            success:function (response) {

                if(response.status == 1){
                    iconAccion('ico','fa-spinner fa-pulse fa-fw','fa-check');
                }else{
                    iconAccion('ico','fa-spinner fa-pulse fa-fw','fa-times');
                }
                open_modal_message('modal_body_message','modal_message',response.msg);
            }
        });
    }

    function obtener_horario(id_empleado) {

        $("#btn_store_horas_extras").attr('disabled',true);

        var fecha = $("#fecha_solicitud_1").val().split("/");

        anno_mes_feriado = fecha[1]+"/"+fecha[2];

        let fecha_solicitud = fecha[2]+"-"+fecha[1]+"-"+fecha[0]

        console.log(moment().diff(moment(fecha_solicitud), 'days'))

        $("#hora_expirada").remove()
        $("div.inputs_horas div, div.input_comentario div, #btn_store_horas_extras").show()

        $.ajax({
            method   : 'GET',
            url      : '{{route('search.feriado',0)}}',
            data     : {
                fecha_anno_mes_feriado : fecha[1]+"/"+fecha[2],
                data : true,
                id_empleado : id_empleado,
                fecha_solicitud
            },
            success: function (response) {

                if(moment().diff(moment(fecha_solicitud), 'days') > response.dias_habiles){

                    $("div.inputs_horas div, div.input_comentario div, #btn_store_horas_extras").hide()

                    $("div.inputs_horas").append(
                        `<div class="col-md-12" id="hora_expirada">
                            <div class="alert alert-info text-center " style="font-size:16px">
                                El tiempo para poder solicitar horas extras el día seleccionado expiró, debe solictar las horas extras máximo ${response.dias_habiles} días despues de haberla laborado
                            </div>
                        </div>`
                    )

                    return false
                }

                feriado = false;
                for (let i = 0; i < response.annos_mes_feriado.length; i++) {

                    fecha_feriado = response.annos_mes_feriado[i].fecha_feriado.split("-");
                    n_fecha_feriado = fecha_feriado[2]+"/"+fecha_feriado[1]+"/"+fecha_feriado[0];

                    if(n_fecha_feriado === $("#fecha_solicitud_1").val()){
                        feriado = true;
                        break;
                    }
                }

                if(feriado && response.asignacion_horario != null){

                    $("#hora_llegada_1").val(response.asignacion_horario.desde);
                    $("#hora_salida_1").val(response.asignacion_horario.hasta);
                    desde = response.asignacion_horario.desde.split(":")[0];
                    hasta = response.asignacion_horario.hasta.split(":")[0];

                    $("#cantidad_horas_1").val(hasta-desde);
                    $("#btn_store_horas_extras").attr('disabled',false);
                    $("#error_fecha_1" ).html("");
                    $(".inputs_horas").addClass('hide');

                }else{

                    $(".inputs_horas").removeClass('hide');
                    $.ajax({
                        method   : 'GET',
                        url      : '{{route('horas-extras.show',0)}}',
                        data    :{
                            id_empleado  : id_empleado,
                            fecha        : fecha[2]+"-"+fecha[1]+"-"+fecha[0]
                        },
                        success: function (response) {

                            if(response.asignacionHorario!=null){

                                $("#hora_salida_1").val(response.asignacionHorario.hasta);
                                $("#hora_llegada_1").val(response.asignacionHorario.desde);

                                if(response.fin_semana){
                                    var start = moment.duration(response.asignacionHorario.desde, "HH:mm"),
                                        end   = moment.duration(response.asignacionHorario.hasta, "HH:mm"),
                                        diff = end.subtract(start);

                                    var horas = diff.hours();
                                    var minutos = diff.minutes();

                                    if(minutos<10)
                                        minutos = "0"+minutos;
                                    $("#hora_desde_1,#hora_hasta_1").val("");
                                    $("#cantidad_horas_1").val(horas+":"+minutos);
                                }
                                $("#error_fecha_1" ).html("");
                                $("#hora_hasta_1").attr('disabled',false);
                            }else{
                                $("#error_fecha_1" ).html("<span class='error' id='error'>No posee horas extras configuradas para este día</span>");
                                $("#hora_hasta_1").attr('disabled',true);

                            }
                        }
                    });
                    $("#btn_store_horas_extras").attr('disabled',false);
                }

            }
        });



    }

</script>
