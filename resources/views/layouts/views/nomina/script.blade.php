<script>

    $(window).ready(function () {
       $(".tool-plus i").removeClass('fa-minus');
        $(".tool-plus i").addClass('fa-plus');
    });

    function form_carga_roles() {
        $.ajax({
            method: 'GET',
            url: '{{route('nomina.create')}}',
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-xs');
            }
        });
    }

    function upload_rol_firmado() {
        if ($('#form_add_imagenes_roles').valid()) {

            var formData = new FormData($("#form_add_imagenes_roles")[0]);

            bootbox.confirm({
                size: null,
                message: "¿Esta seguro que desea subir estas imágenes de roles?",
                callback: function (result) {
                    if (result) {
                        iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');
                        $.ajax({
                            method: 'POST',
                            url: '{{route('nomina.store')}}',
                            data   : formData,
                            processData: false,
                            contentType: false,
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
    }

    function eliminar_imagen_rol(id_imagen,nombre_imagen) {
        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea Eliminar la imágen de este rol?",
            callback: function (result) {
                if (result) {
                    iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');
                    $.ajax({
                        method: 'DELETE',
                        url: '{{route('nomina.destroy',0)}}',
                        data   : {
                            id_imagen_rol : id_imagen,
                            nombre_imagen : nombre_imagen
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

    function generar_nomina() {

        $("#a_aprobar_nomina").css('visibility','hidden');
        $.ajax({
            method: 'GET',
            url : '{{route('generar.nomina')}}',
            data :{
                fecha : $("#fecha_nomina").val(),
                store: 0
            },
            success: function (response) {
                $(".listado_nomina").html(response);
            }
        });
    }

    function aprobar_nomina() {
        bootbox.confirm({
            size: null,
            message: "¿Esta seguro de aprobar la nómina generada?",
            callback: function (result) {
                if (result) {
                    $("#a_aprobar_nomina").css('visibility', 'hidden');
                    $.ajax({
                        method: 'GET',
                        url: '{{route('generar.nomina')}}',
                        data: {
                            fecha: $("#fecha_nomina").val(),
                            store: 1
                        },
                        success: function (response) {
                            $(".listado_nomina").html(response);
                        }
                    });
                }
            }
        });
    }

    function generar_informe_nomina() {
        load(1);
        $.ajax({
            method: 'POST',
            url        : '{{route('genera.informe_nomina')}}',
            data :{
                fecha_nomina : $("#fecha_nomina").val(),
            },
            success: function (response) {
                $(".informe_nomina").html(response);;
            }
        }).always(function () {
            load(0);
        });
    }

    function alcance_nomina(id_nomina,empleado,relacion_dependencia) {
        $.ajax({
            method: 'GET',
            url: '{{route('genera.crear-alcance-nomina')}}',
            data:{
                id_nomina: id_nomina,
                empleado: empleado,
                relacion_dependencia: relacion_dependencia
            },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-xs');
            }
        });
    }

    function generar_alcance_nomina(id_nomina){

        if ($('#form_alcance_nomina').valid()) {

            bootbox.confirm({
                size: null,
                message: "¿Esta seguro que desea realizar este alcance de nómina?",
                callback: function (result) {
                    if (result) {
                        $.ajax({
                            method: 'POST',
                            url: '{{route('genera.store-alcance-nomina')}}',
                            data   : {
                                id_nomina : id_nomina,
                                sueldo : $('#sueldo').val(),
                                hora_extra: $("#he").val(),
                                comision: $("#comision").val(),
                                bono: $("#bono").val(),
                                dcmo_3ro: $("#10mo3ero").val(),
                                dcmo_4to: $("#10mo4to").val(),
                                fondo_reserva: $("#fondo_reserva").val(),
                                comentario: $("#comentario").val(),
                                user_login_id: '{{session('dataUsuario')['id_usuario_log']}}'
                            },
                            success: function (response) {
                                open_modal_message('modal_body_message', 'modal_message', response.msg);
                            }
                        });
                    }

                }
            });
        }

    }

    function form_chash(fecha, tipo){
        $.ajax({
            method: 'POST',
            url: '{{url('form-cash-management')}}',
            data   : {
                fecha: fecha,
                tipo : tipo
            },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response);
            }
        });
    }

    function download_cash_management(fecha,tipo){

        let pagos =[]
        $.each($('input.check_pago_nomina'),(i,j) => {
            if($(j).is(':checked')){
                pagos.push({
                    'id_empleado': j.id
                })
            }
        })

        if(!pagos.length){
            let msg = `<div class='alert alert-error'>Debe seleccionar al menos un empleado para realizar el pago</div>`
            open_modal_message('modal_body_message','modal_message',msg)
            return false
        }

        $.ajax({
            method: 'POST',
            url : '{{route('file.cash_managment')}}',
            data :{
                fecha : fecha,
                tipo : tipo,
                empleados: pagos
            },
            success: function (response) {

                let url = window.location.protocol+'//'+window.location.hostname

                if(url=='{{env('URL_INNOFARM')}}'){
                    empresa = 'INNOFARM'
                }else if (url=='{{env('URL_INNOCLINICA')}}'){
                    empresa = 'INNOCLINICA'
                }else{
                    empresa = 'SERDIMED'
                }

                let a = document.createElement("a");
                a.href = "data:text/plain;base64," + response;
                a.download = 'cash_managment_nomina_'+empresa+' '+moment(fecha).format("MM-YYYY")+'.txt';
                a.click();

            }
        });

    }

    function store_referencia_bancaria(fecha,tipo){

        if($("#form_cash_management_nomina").valid()){

            let pagos =[]
            $.each($('input.check_pago_nomina'),(i,j) => {
                if($(j).is(':checked')){
                    pagos.push({
                        'id_empleado': j.id
                    })
                }
            })

            if(!pagos.length){
                let msg = `<div class='alert alert-error'>Debe seleccionar al menos un empleado para realizar el pago</div>`
                open_modal_message('modal_body_message','modal_message',msg)
                return false
            }

            $.ajax({
                method: 'POST',
                url: '{{url('store-referencia-bancaria')}}',
                data: {
                    referencia : $("#referencia_bancaria").val(),
                    fecha : fecha,
                    tipo : tipo,
                    empleados:pagos
                },
                success: function (response) {
                    open_modal_message('modal_body_message','modal_message',response.msg);
                    if(response.success)
                        $(".modal").on('hidden.bs.modal', function () {
                            location.reload();
                        });
                }
            });
        }

    }

    function form_decimos(tipo){
        $.ajax({
            method: 'POST',
            url: '{{url('form-cash-management-decimos')}}',
            data   : {
                tipo: tipo,
            },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-lg');
            }
        });
    }

    function download_cash_management_decimo(tipo){

        $.ajax({
            method: 'POST',
            url : '{{url('file-cash-managment-decimos')}}',
            data :{
                tipo : tipo
            },
            success: function (response) {

                let url = window.location.protocol+'//'+window.location.hostname

                if(url=='{{env('URL_INNOFARM')}}'){
                    empresa = 'INNOFARM'
                }else if (url=='{{env('URL_INNOCLINICA')}}'){
                    empresa = 'INNOCLINICA'
                }else{
                    empresa = 'SERDIMED'
                }

                let a = document.createElement("a");
                a.href = "data:text/plain;base64," + response;
                a.download = 'cash_managment_decimos_'+empresa+'.txt';
                a.click();

            }
        });

    }

    function store_referencia_bancaria_decimo(tipo){

        if($("#form_cash_management_decimo").valid()){

            $.ajax({
                method: 'POST',
                url: '{{url('store-referencia-bancaria-decimos')}}',
                data: {
                    tipo : tipo,
                    referencia : $("#referencia_bancaria").val()
                },
                success: function (response) {
                    open_modal_message('modal_body_message','modal_message',response.msg);
                    if(response.success)
                        $(".modal").on('hidden.bs.modal', function () {
                            location.reload();
                        });
                }
            });

        }

    }

    function form_alcances_nomina(tipo){
        $.ajax({
            method: 'POST',
            url: '{{url('form-cash-management-alcance-nomina')}}',
            data: {},
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-lg');
            }
        });
    }

    function download_cash_management_alcances_nomina(tipo){

        $.ajax({
            method: 'POST',
            url : '{{url('file-cash-managment-alcance-nomina')}}',
            data :{},
            success: function (response) {

                let url = window.location.protocol+'//'+window.location.hostname

                if(url=='{{env('URL_INNOFARM')}}'){
                    empresa = 'INNOFARM'
                }else if (url=='{{env('URL_INNOCLINICA')}}'){
                    empresa = 'INNOCLINICA'
                }else{
                    empresa = 'SERDIMED'
                }

                let a = document.createElement("a");
                a.href = "data:text/plain;base64," + response;
                a.download = 'cash_managment_alcance_nomina_'+empresa+'.txt';
                a.click();

            }
        });

    }

    function store_referencia_bancaria_alcances_nomina(){

        if($("#form_cash_management_decimo").valid()){

            $.ajax({
                method: 'POST',
                url: '{{url('store-referencia-bancaria-alcance-nomina')}}',
                data: {
                    referencia : $("#referencia_bancaria").val()
                },
                success: function (response) {
                    open_modal_message('modal_body_message','modal_message',response.msg);
                    if(response.success)
                        $(".modal").on('hidden.bs.modal', function () {
                            location.reload();
                        });
                }
            });

        }

    }

    function form_liquidacion(fecha){
        $.ajax({
            method: 'POST',
            url: '{{url('form-cash-management-liquidacion')}}',
            data: { },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response);
            }
        });
    }

    function download_cash_management_liquidacion(tipo){

        let pagos =[]
        $.each($('input.check_pago_nomina'),(i,j) => {
            if($(j).is(':checked')){
                pagos.push(j.id)
            }
        })

        if(!pagos.length){
            let msg = `<div class='alert alert-error'>Debe seleccionar al menos un empleado para realizar el pago</div>`
            open_modal_message('modal_body_message','modal_message',msg)
            return false
        }

        $.ajax({
            method: 'POST',
            url : '{{url('file-cash-managment-liquidacion')}}',
            data :{
                empleados: pagos
            },
            success: function (response) {

                let url = window.location.protocol+'//'+window.location.hostname

                if(url=='{{env('URL_INNOFARM')}}'){
                    empresa = 'INNOFARM'
                }else if (url=='{{env('URL_INNOCLINICA')}}'){
                    empresa = 'INNOCLINICA'
                }else{
                    empresa = 'SERDIMED'
                }

                let a = document.createElement("a");
                a.href = "data:text/plain;base64," + response;
                a.download = 'cash_managment_alcance_nomina_'+empresa+'.txt';
                a.click();

            }
        });

    }

    function store_referencia_bancaria_liquidacion(){

        let pagos =[]
        $.each($('input.check_pago_nomina'),(i,j) => {
            if($(j).is(':checked')){
                pagos.push(j.id)
            }
        })

        if(!pagos.length){
            let msg = `<div class='alert alert-error'>Debe seleccionar al menos un empleado para realizar el pago</div>`
            open_modal_message('modal_body_message','modal_message',msg)
            return false
        }


        if($("#form_cash_management_liquidaciones").valid()){

            $.ajax({
                method: 'POST',
                url: '{{url('store-referencia-bancaria-liquidacion')}}',
                data: {
                    referencia : $("#referencia_bancaria").val(),
                    empleados: pagos
                },
                success: function (response) {
                    open_modal_message('modal_body_message','modal_message',response.msg);
                    if(response.success)
                        $(".modal").on('hidden.bs.modal', function () {
                            location.reload();
                        });
                }
            });

        }

    }

</script>
