<script>

    function add_tipo_comision(id_tipo_comision) {
        $.ajax({
            method   : 'GET',
            url      : '{{route('tipo-comisiones.create')}}',
            data    :{
                id_tipo_comision : id_tipo_comision
            },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-lg');
            }
        });
    }


    function store_tipo_comision(){

        if($('#form_tipo_comsion').valid()){

            iconAccion('ico','fa-floppy-o','fa-spinner fa-pulse fa-fw');
            $.ajax({
                method : 'POST',
                url    : '{{route('tipo-comisiones.store')}}',
                data   :{
                    _token           : '{{ csrf_token() }}',
                    nombre           : $("#nombre").val(),
                    monto_estandar   : $("#monto_estandar").val(),
                    descripcion      : $("#descripcion").val(),
                    id_tipo_comision : $("#id_tipo_comision ").val(),
                    decimo_tercero   : $("#decimo_tercero ").val()
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

    function delete_tipo_comision(id_tipo_comision) {
        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea eliminar este tipo de comisión?",
            callback: function (result) {
                if (result) {
                    $.ajax({
                        type: 'DELETE',
                        url: '{{route('tipo-comisiones.destroy',0)}}',
                        data: {
                            id_tipo_comision: id_tipo_comision
                        },
                        success: function (response) {
                            open_modal_message('modal_body_message', 'modal_message', response);
                        }
                    });
                }
            }
        });
    }

    function update_comision(id_tipo_comision,estado) {
        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea cambiar e estado de este tipo de comisión?",
            callback: function (result) {
                if (result) {
                    $.ajax({
                        type: 'PATCH',
                        url: '{{route('tipo-comisiones.update',0)}}',
                        data: {
                            id_tipo_comision: id_tipo_comision,
                            estado     : estado
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