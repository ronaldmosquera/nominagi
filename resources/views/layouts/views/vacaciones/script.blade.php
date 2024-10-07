<script>

    function add_vacaciones(id_vacaciones,id_empleado) {
        $.ajax({
            method   : 'GET',
            url      : '{{route('vacaciones.show',0)}}',
            success: function (response){
                if(response > 0){
                    var msg = '<div class="alert alert-info" role="alert" style="margin: 0"><i class="fa fa-info-circle" aria-hidden="true"></i> Usted posee vacaciones No aprobadas o aprobadas, cuando finalicen las aprobadas o elimine las no aprobadas podrá solicitar otras! </div>';
                    open_modal_message('modal_body_message','modal_message',msg);
                }else{
                    $.ajax({
                        method   : 'GET',
                        url      : '{{route('vacaciones.create')}}',
                        data    :{
                            id_vacaciones : id_vacaciones,
                            id_empleado   : id_empleado
                        },
                        success: function (response) {
                            open_modal_form('body_modal','modal-default',response,'modal-lg');
                        }
                    });
                }
            }
        });
    }

    function verificar_fechas() {

        $.ajax({
            method   : 'GET',
            url      : '{{action('VacacionesController@diasVacaciones')}}',
            success: function (response) {

                var diasVacaciones = response[0].vacaciones_dias_entre_semana + response[0].vacaciones_dias_fines_semana;

                var diferencia_dias = moment($("#fecha_fin").val()).diff(moment($("#fecha_inicio").val()), 'days')+1;

                isNaN(diferencia_dias) ? dias = '' : dias = diferencia_dias;

                $("#cant_dias").val(dias);

                dias_transcurridos = (moment($("#fecha_inicio").val()).diff(moment(response[2].fecha_expedicion_contrato), 'days'));

                if(dias_transcurridos <= 364){
                    error('Debe seleccionar una fecha desde mayor al: '+moment(response[2].fecha_expedicion_contrato).add(366, 'days').format("DD-MM-YYYY"));
                    return false;
                }

                if(moment($("#fecha_inicio").val()).isBefore(moment().format("YYYY-MM-DD")) || moment($("#fecha_fin").val()).isBefore(moment().format("YYYY-MM-DD"))){
                    error('Las fechas seleccionadas deben ser mayores al día de hoy');
                    return false;
                }

                if(response[2].vacaciones < dias){
                    error('Sólo posee '+response[2].vacaciones+' días acumulados de vacaciones');
                    return false;
                }

                if(diferencia_dias < 1){
                    error('Le fecha de fin debe ser mayor a la fecha de inicio');
                    return false;
                }

                /* if(diferencia_dias > diasVacaciones) {
                    error('Las vacaciones deben ser tomadas en un periodo de ' + diasVacaciones + ' días anuales');
                    return false;
                } */

                if($("#fecha_inicio").val().length < 1 || $("#fecha_fin").val().length < 1){
                    error('Debe seleccionar ambas fechas');
                    return false;
                }


                if(response[1] != null && $("#id_vacacion").val().length < 1){
                    var fecha_fin = moment(response[1].fecha_fin).format("YYYY-MM-DD");

                    if(response[1] !== " " && (moment(fecha_fin).isAfter(moment($("#fecha_inicio").val()).format("YYYY-MM-DD"))) || moment(fecha_fin).isSame(moment($("#fecha_inicio").val()).format("YYYY-MM-DD"))) {
                        error('Sus últimas vacaciones fueron hasta el ' + moment(fecha_fin).format("DD/MM/YYYY") + ', la fecha de inicio de la actuales vacaciones debe ser superior a la antes mencionada');
                        return false;
                    }
                }

                $("#fecha_inicio,#fecha_fin").removeClass('error');
                $("#btn_store_vacaciones").attr('disabled',false);
                $("#msg").html('');
            }
        });
    }

    function store_vacaciones() {

        if($('#form_vacaciones').valid()){

            $("#btn_store_vacaciones").attr('disabled',true);
            var diferencia_dias = moment($("#fecha_fin").val()).diff(moment($("#fecha_inicio").val()), 'days')+1;
            var desde = moment($("#fecha_inicio").val()).format('YYYY-MM-DD');

            var entre_semana = 0;
            var fin_semana   = 0;

            for(var i=1; i<=diferencia_dias; i++){

                if(moment(desde).isoWeekday() == 1 || moment(desde).isoWeekday() == 2 || moment(desde).isoWeekday() == 3 || moment(desde).isoWeekday() == 4 || moment(desde).isoWeekday() == 5){
                    entre_semana++;

                }else if(moment(desde).isoWeekday() == 6 || moment(desde).isoWeekday() == 7){
                    fin_semana++
                }
                desde = moment(desde).add(1,'days').format('YYYY-MM-DD');
            }

            iconAccion('ico','fa-floppy-o','fa-spinner fa-pulse fa-fw');
            $.ajax({
                method : 'POST',
                url    : '{{route('vacaciones.store')}}',
                data   : {
                    fecha_inicio : $("#fecha_inicio").val(),
                    fecha_fin    : $("#fecha_fin").val(),
                    cant_dias    : $("#cant_dias").val(),
                    entre_semana : entre_semana,
                    fin_semana   : fin_semana,
                    id_vacacion  : $("#id_vacacion").val(),
                    atrasadas    : $("#atrasadas").is(":checked"),
                    /* periodo_desde: $("#periodo_desde").val(),
                    periodo_hasta: $("#periodo_hasta").val(), */
                },
                success:function (response) {

                    if(response.status == 1){
                        iconAccion('ico','fa-spinner fa-pulse fa-fw','fa-check');
                    }else{
                        iconAccion('ico','fa-spinner fa-pulse fa-fw','fa-times');
                    }
                    open_modal_message('modal_body_message','modal_message',response.msg);
                    $("#btn_store_vacaciones").attr('disabled',false);
                }
            });
        }
    }

    function delete_vacaciones(id_vacaciones) {
        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea eliminar esta solicitud de vacaciones?",
            callback: function (result) {
                if (result) {
                    $.ajax({
                        type: 'POST',
                        url: '{{route('delete.vacaiones')}}',
                        data: {
                            id_vacaciones: id_vacaciones
                        },
                        success: function (response) {
                            open_modal_message('modal_body_message', 'modal_message', response.msg);
                            if(response.success){
                                $(".modal").on('hidden.bs.modal', function () {
                                    location.reload()
                                });
                            }
                        }
                    });
                }
            }
        });
    }

    function save_success_vacaciones(estado) {

        arrIdVacacionesAprobadas = [];
        $.each($('input:checkbox[name=check_vacaciones]:checked'), function (i, j) {
            arrIdVacacionesAprobadas.push([j.value,j.id]);
        });

        if(arrIdVacacionesAprobadas.length < 1){
            open_modal_message('modal_body_message', 'modal_message',
                '<div class="alert alert-warning" role="alert" style="margin-bottom: 10px"> Debe seleccinar al menos una solicitud de vacaciones para aprobar </div>');
            return false;
        }

        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea aprobar las vacaciones seleccionadas?",
            callback: function (result) {
                if (result) {
                    iconAccion('ico','fa-floppy-o','fa-spinner fa-pulse fa-fw');

                    $("#btn_vacaciones").attr('disabled',true);
                    $.ajax({
                        method: 'POST',
                        url: '{{route('vista.success_vacaciones_admin')}}',
                        data: {
                            arrIdVacacionesAprobadas   : arrIdVacacionesAprobadas,
                            estado                     : estado
                        },
                        success: function (response) {
                            if (response.status == 1) {
                            } else {
                                iconAccion('ico', 'fa-spinner fa-pulse fa-fw', 'fa-check');
                                iconAccion('ico', 'fa-spinner fa-pulse fa-fw', 'fa-times');
                            }
                            open_modal_message('modal_body_message', 'modal_message', response.msg);
                            $("#btn_vacaciones").attr('disabled',false);
                        }
                    });
                }
            }
        });
    }

    function error(msg) {
        $("#msg").html('<i class="fa fa-info-circle" aria-hidden="true"></i> <b>'+msg+'</b>');
        $("#fecha_inicio,#fecha_fin").addClass('error');
        $("#btn_store_vacaciones").attr('disabled',true);
    }

    function form_comentario_vacaciones_no_aprobadas(check,id_empleado) {

        if($("#"+check.id).is(':checked')){
            $.ajax({
                method   : 'GET',
                url      : '{{route('vista.form_comentario_vacaciones_no_aprobadas')}}',
                data    :{
                    id_vacaciones : check.id,
                    id_empleado   : id_empleado
                },
                success: function (response) {
                    open_modal_form('body_modal','modal-default',response,'modal-xs');
                }
            });
        }
    }

    function store_comentario_vacaciones_no_aprobadas(id_vacaciones) {

        if($('#form_comentario_no_aprobadas').valid()){

            $("#btn_store_vacaciones_no_aprobadas").attr('disabled', true);
            iconAccion('ico','fa-floppy-o','fa-spinner fa-pulse fa-fw');
            $.ajax({
                method: 'POST',
                url: '{{route('store.comentario_vacaciones_no_aprobadas')}}',
                data: {
                    comentario    : $("#comentario").val(),
                    id_vacaciones : id_vacaciones
                },
                success: function (response) {

                    if (response.status == 1) {
                        iconAccion('ico', 'fa-spinner fa-pulse fa-fw', 'fa-check');
                    } else {
                        iconAccion('ico', 'fa-spinner fa-pulse fa-fw', 'fa-times');
                    }
                    open_modal_message('modal_body_message', 'modal_message', response.msg);
                    $("#btn_store_vacaciones_no_aprobadas").attr('disabled', false);
                }
            });
        }
    }

    function edit_vacaciones_admin(id_vacaciones,id_empleado) {
        $.ajax({
            method   : 'GET',
            url      : '{{route('vacaciones.show',0)}}',
            success: function (response){
                if(response > 0){
                    var msg = '<div class="alert alert-info" role="alert" style="margin: 0"><i class="fa fa-info-circle" aria-hidden="true"></i> Usted posee vacaciones No aprobadas o aprobadas, cuando finalicen las aprobadas o elimine las no aprobadas podrá solicitar otras! </div>';
                    open_modal_message('modal_body_message','modal_message',msg);
                }else{
                    $.ajax({
                        method   : 'GET',
                        url      : '{{route('vista.edit_vacaciones_admin')}}',
                        data    :{
                            id_vacaciones : id_vacaciones,
                            id_empleado   : id_empleado
                        },
                        success: function (response) {
                            open_modal_form('body_modal','modal-default',response,'modal-lg');
                        }
                    });
                }
            }
        });
    }

    function store_edit_vacaciones_admin() {
        if($('#form_vacaciones').valid()){

            $("#btn_store_vacaciones").attr('disabled',true);
            var diferencia_dias = moment($("#fecha_fin").val()).diff(moment($("#fecha_inicio").val()), 'days')+1;
            var desde = moment($("#fecha_inicio").val()).format('YYYY-MM-DD');

            var entre_semana = 0;
            var fin_semana   = 0;

            for(var i=1; i<=diferencia_dias; i++){

                if(moment(desde).isoWeekday() == 1 || moment(desde).isoWeekday() == 2 || moment(desde).isoWeekday() == 3 || moment(desde).isoWeekday() == 4 || moment(desde).isoWeekday() == 5){
                    entre_semana++;

                }else if(moment(desde).isoWeekday() == 6 || moment(desde).isoWeekday() == 7){
                    fin_semana++
                }
                desde = moment(desde).add(1,'days').format('YYYY-MM-DD');
            }

            iconAccion('ico','fa-floppy-o','fa-spinner fa-pulse fa-fw');
            $.ajax({
                method : 'POST',
                url    : '{{route('store.edit_vacaciones_admin')}}',
                data   : {
                    fecha_inicio : $("#fecha_inicio").val(),
                    fecha_fin    : $("#fecha_fin").val(),
                    cant_dias    : $("#cant_dias").val(),
                    entre_semana : entre_semana,
                    fin_semana   : fin_semana,
                    id_vacacion  : $("#id_vacacion").val(),
                    atrasadas    : $("#atrasadas").is(":checked"),
                    periodo_desde: $("#periodo_desde").val(),
                    periodo_hasta: $("#periodo_hasta").val(),
                },
                success:function (response) {

                    if(response.status == 1){
                        iconAccion('ico','fa-spinner fa-pulse fa-fw','fa-check');
                    }else{
                        iconAccion('ico','fa-spinner fa-pulse fa-fw','fa-times');
                    }
                    open_modal_message('modal_body_message','modal_message',response.msg);
                    $("#btn_store_vacaciones").attr('disabled',false);
                }
            });
        }
    }

    function verificar_fechas_admin(id_empleado) {

        $.ajax({
            method   : 'GET',
            url      : '{{action('AdminVacacionesController@diasVacacionesAdmin')}}',
            data     :{
                id_empleado : id_empleado
            },
            success: function (response) {

                var diasVacaciones = response[0].vacaciones_dias_entre_semana + response[0].vacaciones_dias_fines_semana;

                var diferencia_dias = moment($("#fecha_fin").val()).diff( moment($("#fecha_inicio").val()), 'days')+1;

                isNaN(diferencia_dias) ? dias = '' : dias = diferencia_dias;

                $("#cant_dias").val(dias);

                if(diferencia_dias < 1){
                    error('La fecha de inicio debe ser mayor a la fecha de fin');
                    return false;
                }

                if(response[2].vacaciones < dias){
                    error('El empleado sólo posee '+response[2].vacaciones+' días acumulados de vacaciones');
                    return false;
                }

                if(diferencia_dias > diasVacaciones) {
                    error('Las vacaciones deben ser tomadas en un periodo de ' + diasVacaciones + ' días anuales');
                    return false;
                }

                if($("#fecha_inicio").val().length < 1 || $("#fecha_fin").val().length < 1){
                    error('Debe seleccionar ambas fechas');
                    return false;
                }

                /* if(moment($("#fecha_inicio").val()).isBefore(moment().format("YYYY-MM-DD")) || moment($("#fecha_fin").val()).isBefore(moment().format("YYYY-MM-DD"))){
                    error('Las fechas seleccionadas deben ser mayores al día de hoy');
                    return false;
                } */
                if(response[1] != null && $("#id_vacacion").val().length < 1){
                    var fecha_fin = moment(response[1].fecha_fin).format("YYYY-MM-DD");

                    if(response[1] !== " " && (moment(fecha_fin).isAfter(moment($("#fecha_inicio").val()).format("YYYY-MM-DD"))) || moment(fecha_fin).isSame(moment($("#fecha_inicio").val()).format("YYYY-MM-DD"))) {
                        error('Sus últimas vacaciones fueron hasta el ' + moment(fecha_fin).format("DD/MM/YYYY") + ', la fecha de inicio de la actuales vacaciones debe ser superior a la antes mencionada');
                        return false;
                    }
                }

                $("#fecha_inicio,#fecha_fin").removeClass('error');
                $("#btn_store_vacaciones").attr('disabled',false);
                $("#msg").html('');
            }
        });
    }

</script>
