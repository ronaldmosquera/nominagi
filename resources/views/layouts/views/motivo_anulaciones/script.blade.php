<script>
    function add_motivo_anulacion_contrato(id_motivo_anulacion) {
        $.ajax({
            method   : 'GET',
            url      : '{{route('anulacion-contrato.create')}}',
            data    :{
                id_motivo_anulacion : id_motivo_anulacion
            },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-lg');
            }
        });
    }
    
    function store_motivo_anulacion() {
        if($('#form_motivo_anulacion').valid()){

            iconAccion('ico','fa-floppy-o','fa-spinner fa-pulse fa-fw');
            $.ajax({
                method : 'POST',
                url    : '{{route('anulacion-contrato.store')}}',
                data   :{
                    _token                       : '{{ csrf_token() }}',
                    nombre                       : $("#nombre").val(),
                    descripcion                  : $("#descripcion").val(),
                    id_motivo_anulacion          : $("#id_motivo_anulacion").val(),
                    calculo_deshaucio            : $("#id_calculo_deshaucio").val(),
                    calculo_despido_intempestivo : $("#id_calculo_despido_intempestivo").val(),
                    calcula_liquidacion          : $("#calcula_liquidacion").val(),
                },
                success:function (response) {

                    if(response.status == 1){
                        iconAccion('ico','fa-spinner fa-pulse fa-fw','fa-check');
                        $("#id_motivo_anulacion").val() != '' ? '': $("#nombre,#descripcion").val('');

                    }else{
                        iconAccion('ico','fa-spinner fa-pulse fa-fw','fa-times');
                    }
                    open_modal_message('modal_body_message','modal_message',response.msg,true);
                }
            });
        }
    }
    
    function update_status(id_motivo_anulacion,estado) {
        $.ajax({
            type  : 'PATCH',
            url   : '{{route('anulacion-contrato.update',0)}}',
            data  :{
                id_motivo_anulacion : id_motivo_anulacion,
                estado              : estado
            },
            success: function (response) {
                open_modal_message('modal_body_message','modal_message',response);
            }
        });
    }
    
    function delete_motivo_anulacion(id_motivo_anulacion) {
        $.ajax({
            type  : 'DELETE',
            url   : '{{route('anulacion-contrato.destroy',0)}}',
            data  :{
                id_motivo_anulacion : id_motivo_anulacion
            },
            success: function (response) {
                open_modal_message('modal_body_message','modal_message',response);
            }
        });
    }

</script>