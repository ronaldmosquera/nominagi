<script>

    tipo_doc()

    function store_documento() {

        /* if($('#form_store_documento').valid()){ */

            iconAccion('ico','fa-floppy-o','fa-spinner fa-pulse fa-fw');
            var formData = new FormData($("#form_store_documento")[0]);
            formData.append('cuerpo_documento',CKEDITOR.instances['cuerpo_documento'].getData());

            $.ajax({
                method : 'POST',
                url    : '{{route('documentos.store')}}',
                data   : formData,
                processData: false,
                contentType: false,
                success:function (response) {

                    if(response.status == 1){
                        iconAccion('ico','fa-spinner fa-pulse fa-fw','fa-check');
                    }else{
                        iconAccion('ico','fa-spinner fa-pulse fa-fw','fa-times');
                    }
                    open_modal_message('modal_body_message','modal_message',response.msg,true);
                }
            });
        //}
    }

    function update_estado_documento(estado,id_documento) {
        $.ajax({
            method: 'POST',
            url: '{{route('actualizar.documento')}}',
            data: {
                _token: '{{ csrf_token() }}',
                id_documento : id_documento,
                estado       : estado
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

    function generar_documento(id_documento) {
        $.ajax({
            method: 'GET',
            url: '{{route('vista.generar_documento')}}',
            data: {
                _token: '{{ csrf_token() }}',
                id_documento : id_documento,
            },
            success: function (response) {
                //open_modal_message('modal_body_message','modal_message',response.msg);
            }
        });
    }

    function tipo_doc(){

        if($('#tipo_documento').val()=='TRANSCRITO'){
            $('#div_doc_prescrito').removeClass('hidden')
            $('#div_pdf').addClass('hidden')
        }else{
            $('#div_pdf').removeClass('hidden')
            $('#div_doc_prescrito').addClass('hidden')
        }
    }
</script>
