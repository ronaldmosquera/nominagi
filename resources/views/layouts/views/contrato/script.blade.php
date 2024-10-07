<script>


    function store_contrato() {

        if($('#form_add_contrato').valid()) {

            iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');
            $.ajax({
                method: 'POST',
                url: '{{route('contrato.store')}}',
                data: {
                    _token: '{{ csrf_token() }}',
                    id_tipo_contrato: $("#id_tipo_contrato").val(),
                        body_contrato   : CKEDITOR.instances['body_contrato'].getData(),
                    id_contrato     : $("#id_contrato").val()

                },
                success: function (response) {

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

    function update_estado_contrato(id_contrato,estado) {

        $.ajax({
            method: 'POST',
            url: '{{url('contrato/update-estatus')}}',
            data: {
                _token: '{{ csrf_token() }}',
                id_contrato     : id_contrato,
                estado          : estado
            },
            success: function (response) {
                open_modal_message('modal_body_message','modal_message',response,true)
            }
        });
    }

    function delete_contrato(id_contrato) {
        $.ajax({
            type  : 'POST',
            url   : '{{url('contrato/delete')}}',
            data  :{
                id_contrato : id_contrato
            },
            success: function (response) {
                open_modal_message('modal_body_message','modal_message',response,true)
            }
        });
    }

</script>
