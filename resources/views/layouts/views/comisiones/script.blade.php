<script>

    function add_comision(id_comision) {
        $.ajax({
            method   : 'GET',
            url      : '{{route('comisiones.create')}}',
            data    :{
                id_comision : id_comision
            },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-lg');
                setTimeout(function () {
                    if(id_comision != ''){
                        var id_empleado = $("#e").val();
                        $("#fecha_comision_1").val($("#fc").val());
                        $("#id_empleado_1 option[value='"+id_empleado+"']").attr('selected',true);
                        $("#cantidad_1").val($("#c").val());
                        $("#descripcion_1").val($("#d").val());
                    }
                },500);

            }
        });
    }

    function add_inputs() {

        var cant_inputs = $("div#inputs_comsiones div.row").length;

        $("#btn_add_inputs").attr('disabled',true);

        $.ajax({
            method   : 'GET',
            url      : '{{route('vista.add_inputs_comisiones')}}',
            data     : {
                cant_inputs : cant_inputs,
            },
            success: function (response) {

                $("#inputs_comsiones").append(response);

                $("#btn_add_inputs").attr('disabled',false);
                setTimeout(function () {
                    $("#fecha_comision_"+(cant_inputs+1)).val(moment().add(1, 'M').format("YYYY-MM-05"));
                },1000)

            }
        });
    }

    function delete_inputs() {

        var cant_inputs = $("div#inputs_comsiones div.row").length;
        if(cant_inputs<2)
            return false;

        $("div#inputs_comsiones div.row:last-child").remove();
    }

    function store_comsiones() {

        if($('#form_comsiones').valid()) {

            $("#btn_store_comsiones").attr('disabled', true);

            var cant_inputs = $("div#inputs_comsiones div.row").length;

            var arrData = [];

            for (var i = 1; i <= cant_inputs; i++) {
                arrData.push([
                    $("#id_empleado_" + i).val(),     //0
                    $("#fecha_comision_" + i).val(),  //1
                    $("#cantidad_" + i).val(),        //2
                    $("#descripcion_" + i).val(),     //3
                    $("#id_tipo_comision_" + i).val(),//4
                ]);
            }

            iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');
            $.ajax({
                method: 'POST',
                url: '{{route('comisiones.store')}}',
                data: {
                    arrData     : arrData,
                    id_comision : $("#id_comision").val()
                },
                success: function (response) {

                    $("#btn_store_comsiones").attr('disabled', false);

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

    function delete_comision(id_comision) {
        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea eliminar esta comisión?",
            callback: function (result) {
                if (result) {
                    $.ajax({
                        type: 'DELETE',
                        url: '{{route('comisiones.destroy',0)}}',
                        data: {
                            id_comision: id_comision
                        },
                        success: function (response) {
                            open_modal_message('modal_body_message', 'modal_message', response);
                        }
                    });
                }
            }
        });
    }
    
    function asignar_comision(input) {
        $("#btn_store_comsiones").attr('disabled', true);
        $.ajax({
            type  : 'GET',
            url   : '{{route('comisiones.show',0)}}',
            data  :{
                id_tipo_contrato : $("#"+input.id).val()
            },
            success: function (response) {

                $("#cantidad_"+input.name).val(response.estandar);
                $("#btn_store_comsiones").attr('disabled', false);
            }
        });

    }

    function habilitar(input_id) {
        $("#"+input_id).attr('readonly',false);
    }

</script>