<script>
    function editar_empleado(id_empleado) {
        load(1);
        $.ajax({
            method: 'POST',
            url: '{{route('vista.inputs_empleado')}}',
            data: {
                _token  : '{{ csrf_token() }}',
                vista   : 'editar_empleado',
                party_id: id_empleado
            },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-lg');
                load(0);
            }
        });
    }

    function update_status_empleado(id_empleado,estado) {
        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea anular al usuario?",
            callback: function (result) {
                if (result) {
                    $.ajax({
                        method: 'GET',
                        url: '{{route('empleados.edit',0)}}',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id_empleado: id_empleado,
                            estado     : estado
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
        });
    }

    function store_datos_empleado(id_empleado) {

        //var formData = new FormData($("#form_edit_empleado")[0]);
        //formData.append('party_id',id_empleado);
        //formData.append('_token','{{ csrf_token() }}');

        if($("#form_edit_empleado").valid()){

            $.ajax({
                method: 'POST',
                url        : '{{route('update.data_empleado')}}',
                data :{
                    _token : '{{ csrf_token() }}',
                    nombres: $("#nombres").val(),
                    apellidos: $("#apellidos").val(),
                    nacimiento: $("#nacimiento").val(),
                    genero: $("#genero").val(),
                    tipo_identificacion: $("#tipo_identificacion").val(),
                    identificacion: $("#identificacion").val(),
                    telefono: $("#telefono").val(),
                    ciudad: $("#ciudad").val(),
                    provincia: $("#id_provincia").val(),
                    correo: $("#correo").val(),
                    nombre_contacto: $("#nombre_contacto").val(),
                    apellido_contacto: $("#apellido_contacto").val(),
                    telefono_contacto: $("#telefono_contacto").val(),
                    C_V: $("#C_V").val(),
                    party_id: id_empleado
                },
                //data       : formData,
                //processData: false,
                //contentType: false,
                success: function (response) {
                    open_modal_message('modal_body_message','modal_message',response.msg);
                }
            });
        }
    }

</script>
