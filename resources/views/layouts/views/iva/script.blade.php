<script>

    function add_iva(id_iva) {
        $.ajax({
            method   : 'GET',
            url      : '{{route('iva.create')}}',
            data: {
                id_iva: id_iva
            },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-xs');
            }
        });
    }

    function store_iva() {

        if ($('#form_add_iva').valid()) {

            iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');

            $("#btn_store_iva").attr('disabled',true);
            $.ajax({
                method: 'POST',
                url: '{{route('iva.store')}}',
                data   : {
                    nombre : $("#nombre").val(),
                    valor  : $("#valor").val(),
                    id_iva : $("#id_iva").val()
                },
                success: function (response) {
                    $("#btn_store_iva").attr('disabled',false);
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

    function delete_iva(id_iva) {

        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea eliminar este iva?",
            callback: function (result) {
                if (result) {
                    $.ajax({
                        type: 'DELETE',
                        url: '{{route('iva.destroy',0)}}',
                        data: {
                            id_iva: id_iva
                        },
                        success: function (response) {
                            open_modal_message('modal_body_message', 'modal_message', response);
                        }
                    });
                }
            }
        });
    }

</script>