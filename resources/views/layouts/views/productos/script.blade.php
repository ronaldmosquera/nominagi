<script>

    function add_productos() {
        $.ajax({
            method   : 'GET',
            url      : '{{route('productos.create')}}',
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-xs');
            }
        });
    }


    function store_productos() {

        if ($('#form_add_productos').valid()) {

            iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');

            var formData = new FormData($("#form_add_productos")[0]);

            $("#btn_contrataciones").attr('disabled',true);
            $.ajax({
                method: 'POST',
                url: '{{route('productos.store')}}',
                data   : formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $("#btn_contrataciones").attr('disabled',false);
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
    
    function desactivar_producto(id_producto,estado) {
        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea desactivar este producto?",
            callback: function (result) {
                if (result) {
                    $.ajax({
                        type: 'PATCH',
                        url: '{{route('productos.update',0)}}',
                        data: {
                            id_producto: id_producto,
                            estado     : estado
                        },
                        success: function (response) {
                            open_modal_message('modal_body_message', 'modal_message', response.msg);
                        }
                    });
                }
            }
        });
    }
    
    
</script>