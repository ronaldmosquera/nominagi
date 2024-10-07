<script>

    function add_anticipo(id_anticipo) {
        $.ajax({
            method   : 'GET',
            url      : '{{route('anticipos.create')}}',
            data    :{
                id_anticipo : id_anticipo
            },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-lg');
                $("#btn_store_anticipo").attr('disabled',true);
            }
        });
    }

    function verificar_fecha(id) {

        /*if(moment($("#"+id).val()).isBefore(moment().format("YYYY-MM-DD"))){
            $("div#"+id+"_div label").remove();
            $("div#"+id+"_div").append(
                '<label id="fecha_diferir-error" class="error" for="fecha_diferir">La fecha debe ser mayor al día de hoy</label>'
            );
            //$("#btn_store_anticipo").attr('disabled',true);
        }else{
            $("div#"+id+"_div label").remove();
            $("#btn_store_anticipo").attr('disabled',false);
        }*/
    }

    function store_anticipo() {

        if($('#form_anticipo').valid()){
            iconAccion('ico','fa-floppy-o','fa-spinner fa-pulse fa-fw');
            $.ajax({
                method : 'POST',
                url    : '{{route('anticipos.store')}}',
                data   :{
                    _token           : '{{ csrf_token() }}',
                    cantidad         : $("#cantidad").val(),
                    fecha_entrega    : $("#fecha_entrega").val(),
                    fecha_descuento  : $("#fecha_descuento").val(),
                    id_anticipo      : $("#id_anticipo").val(),
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

    function delete_anticipo(id_anticipo) {

        $.ajax({
            type  : 'POST',
            url   : '{{route('delete.anticipos')}}',
            data  :{
                id_anticipo : id_anticipo
            },
            success: function (response) {
                open_modal_message('modal_body_message','modal_message',response.msg);
                if(response.success){
                    $(".modal").on('hidden.bs.modal', function () {
                        location.reload();
                    });
                }
            }
        });
    }

    function verificar_cantidad() {

        var cantidad = $("#cantidad").val();
        $("#btn_store_anticipo").attr('disabled',true);
        $.ajax({
            type  : 'GET',
            url   : '{{route('anticipos.show',0)}}',
            success: function (response) {

                var maximo_anticipo = (response.salario_base*response.porcentaje_anticipo)/100;

                if(parseFloat($("#cantidad").val()) > maximo_anticipo){
                    $("#cantidad_div label").remove();
                    $("#cantidad_div").append(
                        '<label id="fecha_diferir-error" class="error">Sólo puede solicitar anticipos hasta $'+ maximo_anticipo+'  </label>'
                    );

                }else{
                    $("#cantidad_div label").remove();
                    $("#btn_store_anticipo").attr('disabled',false);
                }
            }
        });

    }

    function form_comentario_anticipo_no_aprobado(check) {

        if($("#"+check.id).is(':checked')){
            $.ajax({
                method   : 'GET',
                url      : '{{route('vista.form_comentario_anticipo_no_aprobado')}}',
                data    :{
                    id_anticipo : check.id
                },
                success: function (response) {

                    open_modal_form('body_modal','modal-default',response,'modal-lg');
                }
            });
        }

    }

    function store_comentario_anticipo_no_aprobado(id_anticipo){

        if($('#form_comentario_no_aprobado').valid()){

            $("#btn_store_anticipo_no_aprobado").attr('disabled', true);
            iconAccion('btn_ico','fa-floppy-o','fa-spinner fa-pulse fa-fw');
            $.ajax({
                method: 'POST',
                url: '{{route('store.comentario_anticipo_no_aprobado')}}',
                data: {
                    comentario    : $("#comentario").val(),
                    id_anticipo : id_anticipo
                },
                success: function (response) {

                    if (response.status == 1) {
                        iconAccion('btn_ico', 'fa-spinner fa-pulse fa-fw', 'fa-check');
                        $(".modal").on('hidden.bs.modal', function () { location.reload(); });
                    } else {
                        iconAccion('btn_ico', 'fa-spinner fa-pulse fa-fw', 'fa-times');
                    }
                    open_modal_message('modal_body_message', 'modal_message', response.msg);
                    $("#btn_store_anticipo_no_aprobado").attr('disabled', false);
                }
            });
        }
    }

    function save_success_anticipos() {

        arrIdAnticiposAprobados = [];
        $.each($('input:checkbox[name=check_anticipo]:checked'), function (i, j) {
            arrIdAnticiposAprobados.push(j.value);
        });

        if(arrIdAnticiposAprobados.length<1){
            open_modal_message('modal_body_message', 'modal_message', '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Debe seleccionar al menos un anticipo para aprobar</div>');
            return false;
        }


        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea aprobar los anticipos seleccionados?",
            callback: function (result) {
                if (result) {
                    iconAccion('ico','fa-floppy-o','fa-spinner fa-pulse fa-fw');

                    $("#btn_anticipo").attr('disabled',true);
                    $.ajax({
                        method: 'POST',
                        url: '{{route('aprobar.anticipo',0)}}',
                        data: {
                            arrIdAnticiposAprobados : arrIdAnticiposAprobados,
                        },
                        success: function (response) {
                            if (response.status == 1) {
                                iconAccion('ico', 'fa-spinner fa-pulse fa-fw', 'fa-check');
                                $(".modal").on('hidden.bs.modal', function () { location.reload(); });
                            } else {
                                iconAccion('ico', 'fa-spinner fa-pulse fa-fw', 'fa-times');
                            }
                            open_modal_message('modal_body_message', 'modal_message', response.msg);
                            $("#btn_anticipo").attr('disabled',false);
                        }
                    });
                }
            }
        });
    }


    function edit_anticipo_admin(id_anticipo) {
        $.ajax({
            method   : 'GET',
            url      : '{{route('vista.edit_anticipo_admin')}}',
            data    :{
                id_anticipo : id_anticipo
            },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-lg');
            }
        });
    }

    function store_anticipo_admin() {

        if($('#form_anticipo').valid()){
            iconAccion('ico','fa-floppy-o','fa-spinner fa-pulse fa-fw');
            $.ajax({
                method : 'POST',
                url    : '{{route('store.anticipo_admin')}}',
                data   :{
                    _token           : '{{ csrf_token() }}',
                    //cantidad         : $("#cantidad").val(),
                    fecha_entrega    : $("#fecha_entrega").val(),
                    fecha_descuento  : $("#fecha_descuento").val(),
                    id_anticipo      : $("#id_anticipo").val(),
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

    function from_cash_management_anticipo(){

        $.ajax({
            method: 'POST',
            url: '{{url('form-cash-management-anticipos')}}',
            data   : {},
            success: function (response) {
                open_modal_form('body_modal','modal-default',response);
            }
        });

    }

    function download_cash_management_anticipos(){
        $.ajax({
            method: 'POST',
            url: 'download-cash-management-anticipo',
            data: {},
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
                a.download = 'cash_managment_antipos_'+empresa+'.txt';
                a.click();
            }
        });
    }

    function store_referencia_bancaria_anticipo(){
        $.ajax({
            method: 'POST',
            url: '{{url('store-referencia-bancaria-anticipos')}}',
            data: {
                referencia : $("#referencia_bancaria").val()
            },
            success: function (response) {
                open_modal_message('modal_body_message','modal_message',response.msg);
                if(response.success)
                    $(".modal").on('hidden.bs.modal',() => {
                        location.reload();
                    });
            }
        });
    }

</script>
