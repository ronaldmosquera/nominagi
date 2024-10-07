<script>

    function ver_seccion(id_seccion_menu,id){

        val_check =$("input.check_rol_editar:checked").val();

        if(id==="sub_seccion_menu_edit" && val_check  === undefined){
            open_modal_message('modal_body_message','modal_message','' +
                '<div class="alert alert-danger">Seleccione un rol de usuario</div>');
            $("#"+id).empty();
            return false;
        }

        $.ajax({
            method   : 'GET',
            url      : '{{url('permisos/ver_seccion')}}',
            data     : {
                id_seccion_menu : id_seccion_menu,
                id : id,
                rol : val_check
            },
            success: function (response) {
                $("#"+id).empty().append(response);
            }
        });
    }

    function check_general(check) {
        $.each($("table."+check.id+" input[type='checkbox']"),function(i,j){
            if($(check).is(":checked")){
                $(j).prop('checked',true);
            }else{
                $(j).prop('checked',false);
            }
        });
    }

    function store_configuracion_menu(id_seccion_menu) {

        arr_roles =[];
        arr_check = [];

        $.each($("input.check_rol"),function(i,j){
            if($(j).is(":checked")){
                arr_roles.push({
                    rol : $(j).val()
                });
            }
        });


        $.each($("table.table_check_individual"),function (i,j) {
            $.each($(j).find("tr.tr_check_individual"),function (k,l) {
                if($(l).find('input.check_individual').is(":checked")){
                    arr_check.push({
                        id_ruta_sub_seccion_menu : $(l).find('input.check_individual').val()
                    });
                }
            });
        });

        if(arr_roles.length < 1){
            open_modal_message('modal_body_message', 'modal_message',
                '<div class="alert alert-warning text-center"> ' +
                '<i class="fa fa-exclamation-circle"></i>  Debe seleccionar al menos un rol</div>');
            return false;
        }

        if(arr_check.length < 1){
            open_modal_message('modal_body_message', 'modal_message',
                '<div class="alert alert-warning text-center"> ' +
                '<i class="fa fa-exclamation-circle"></i>  Debe seleccionar al menos un menú a asignar</div>');
            return false;
        }

        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea guardar la configuración de los permisos?",
            callback: function (result) {
                if (result) {
                    $.ajax({
                        type: 'POST',
                        url: '{{url('guardar-permisos')}}',
                        data: {
                            id_seccion_menu: id_seccion_menu,
                            arr_roles: arr_roles,
                            arr_check: arr_check
                        },
                        success: function (response) {
                            open_modal_message('modal_body_message', 'modal_message', response.msg);
                            if(response.success){
                                $(".modal").on('hidden.bs.modal', function () {
                                    location.reload()
                                });
                            }
                        }
                    });
                }
            }
        });

    }

    function seleccionar_check(check,clas) {
        $.each($("input."+clas),function(i,j){
            if($(j).is(":checked")){
                $(j).removeAttr('disabled');
            }else{
                if($(check).is(":checked")){
                    $(j).prop('disabled',true);
                }else{
                    $(j).removeAttr('disabled');
                }
            }
        });
    }
    
    function eliminar_permiso(id_ruta_sub_seccion_menu) {
        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea borrar el permiso para este menú?",
            callback: function (result) {
                if (result) {
                    $.ajax({
                        type: 'POST',
                        url: '{{url('eliminar-permisos')}}',
                        data: {
                            rol : $("input.check_rol_editar:checked").val(),
                            id_ruta_sub_seccion_menu : id_ruta_sub_seccion_menu
                        },
                        success: function (response) {
                            open_modal_message('modal_body_message', 'modal_message', response.msg);
                            if(response.success)
                                ver_seccion($("#id_seccion_menu").val(),'sub_seccion_menu_edit');
                        }
                    });
                }
            }
        });
    }
</script>