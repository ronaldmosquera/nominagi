<script>

    function add_cargo(id_cargo) {
        $.ajax({
            method   : 'GET',
            url      : '{{route('vista.form_cargos')}}',
            data    :{
                id_cargo : id_cargo
            },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-lg');
            }
        });
    }

    function store_cargo(){

        if($('#form_cargo').valid()){

            iconAccion('ico','fa-floppy-o','fa-spinner fa-pulse fa-fw');
            $.ajax({
                method : 'POST',
                url    : '{{route('cargos.store')}}',
                data   :{
                    _token                  : '{{ csrf_token() }}',
                    cargo                   : $("#cargo").val(),
                    descripcion             : $("#descripcion").val(),
                    id_cargo                : $("#id_cargo").val(),
                    sueldo_minimo_sectorial : $("#sueldo_minimo_sectorial").val(),
                    cargo_confianza         : $("#cargo_confianza").val(),
                },
                success:function (response) {

                    if(response.status == 1){
                        iconAccion('ico','fa-spinner fa-pulse fa-fw','fa-check');
                        $("#id_cargo").val() != '' ? '': $("#cargo,#descripcion").val('');
                    }else{
                        iconAccion('ico','fa-spinner fa-pulse fa-fw','fa-times');
                    }
                    open_modal_message('modal_body_message','modal_message',response.msg);
                }
            });
        }
    }

    function delete_cargo(id_cargo) {

        $.ajax({
            type  : 'POST',
            url   : '{{url('cargo/delete')}}',
            data  :{
                id_cargo : id_cargo
            },
            success: function (response) {
                open_modal_message('modal_body_message','modal_message',response);
            }
        });
    }

    function salario_minimo() {
        $("#btn_submit").attr('disabled',true);
        $.ajax({
            method : 'GET',
            url    : '{{route('cargos.show',0)}}',
            success:function (response) {

                if(parseFloat($("#sueldo_minimo_sectorial").val()) < parseFloat(response)){
                    $(".sueldo_sectorial label#message-error").remove();
                    $(".sueldo_sectorial").append("<label id='message-error' class='error' for='descripcion'>Este valor no puede ser menor de $"+response+" </label>")
                }else {
                    $(".sueldo_sectorial label#message-error").remove();
                    $("#btn_submit").attr('disabled',false);
                }
            }
        });


    }
</script>
