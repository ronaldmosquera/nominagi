<script>

function add_tipo_contrato(id_tipo_contrato) {

    $.ajax({
       method   : 'GET',
       url      : '{{route('vista.form_contrato')}}',
        data    :{
            id_tipo_contrato : id_tipo_contrato
        },
        success: function (response) {
            open_modal_form('body_modal','modal-default',response,'modal-lg');
        }
    });
}

function store_tipo_contrato(id_tipo_contrato) {

    if($('#form_add_tipo_contrato').valid()){

        iconAccion('ico','fa-floppy-o','fa-spinner fa-pulse fa-fw');
        $.ajax({
            method : 'POST',
            url    : '{{route('tipo-contrato.store')}}',
            data   :{
                _token                       : '{{ csrf_token() }}',
                nombre                       : $("#nombre").val(),
                descripcion                  : $("#descripcion").val(),
                id_tipo_contrato             : $("#id_tipo_contrato").val(),
                id_tipo_contrato_descripcion : $("#id_tipo_contrato_descripcion").val(),
                horas_extras                 : $("#id_tipo_contrato_descripcion").val() == 2 ? $("#horas_extras").val() : 0,
                relacion_dependencia         : $("#id_tipo_contrato_descripcion").val() == 2 ? $("#relacion_dependencia").val() : 0,
                caducidad                    : $("#id_tipo_contrato_descripcion").val() == 2 ? $("#caducidad").val() : 0,
                sueldo_sectorial             : $("#id_tipo_contrato_descripcion").val() == 2 ? $("#sueldo_sectorial").val() : 0
            },
            success:function (response) {

                if(response.status == 1){
                    iconAccion('ico','fa-spinner fa-pulse fa-fw','fa-check');
                    $("#id_tipo_contrato").val() != '' ? '': $("#nombre,#descripcion").val('');

                }else{
                    iconAccion('ico','fa-spinner fa-pulse fa-fw','fa-times');
                }
                open_modal_message('modal_body_message','modal_message',response.msg);
            }
        });
    }
}

function delete_tipo_contrato(id_tipo_contrato) {

    $.ajax({
        type  : 'POST',
        url   : '{{url('tipo-contrato/delete')}}',
        data  :{
            id : id_tipo_contrato
        },
        success: function (response) {
            open_modal_message('modal_body_message','modal_message',response,true);
        }
    });
}

function update_status(id_tipo_contrato,estado) {

    $.ajax({
        type  : 'POST',
        url   : '{{url('tipo-contrato/update-estatus')}}',
        data  :{
            id     : id_tipo_contrato,
            estado : estado
        },
        success: function (response) {
            open_modal_message('modal_body_message','modal_message',response,true);
        }
    });
}

function configuraciones() {
    if($("#id_tipo_contrato_descripcion").val() == 1)
        $("#config1,#config2").hide();
    else
        $("#config1,#config2").show();
}

</script>
