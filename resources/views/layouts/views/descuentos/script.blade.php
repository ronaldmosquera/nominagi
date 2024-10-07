<script>

    function add_descuento(id_descuento) {
        $.ajax({
            method   : 'GET',
            url      : '{{route('otros-descuentos.create')}}',
            data    :{
                id_descuento : id_descuento
            },
            success: function (response) {

                open_modal_form('body_modal','modal-default',response,'modal-lg')
                setTimeout(function () {

                    if(typeof id_descuento != 'undefined'){
                        $("#btn_delete_descuento_row").remove()
                        var id_empleado = $("#ide").val();
                        $("#fecha_descuento_1").val($("#fd").val());

                        $("#id_empleado_1 option[value='"+id_empleado+"']").attr('selected',true);
                        $("#cantidad_1").val($("#c").val());
                        $("#descripcion_1").val($("#d").val());
                        $("#nombre_1").val($("#nombre").val());
                        set_concepto_descuento('id_empleado_1','id_concepto_1',$("#invoice_item_type_id").val())
                    }

                },2000);
            }
        });
    }

    function add_inputs() {

        var cant_inputs = $("div#inputs_descuentos div.row").length;

        $("#btn_add_inputs").attr('disabled',true);

        $.ajax({
            method   : 'GET',
            url      : '{{route('vista.add_inputs_descuentos')}}',
            data     : {
                cant_inputs : cant_inputs,
            },
            success: function (response) {
                $("#inputs_descuentos").append(response);

                $("#btn_add_inputs").attr('disabled',false);
                setTimeout(function () {
                    $("#fecha_comision_"+(cant_inputs+1)).val(moment().add(1, 'M').format("YYYY-MM-05"));
                },1000)

            }
        });
    }

    function delete_inputs(id_input) {
        $("#"+id_input).remove()
        /* var cant_inputs = $("div#inputs_descuentos div.row").length;

        if(cant_inputs<2) return false;

        $("div#inputs_descuentos div.row:last-child").remove(); */
    }

    function store_descuentos() {

        if($('#form_descuentos').valid()) {

            $("#btn_store_descuentos").attr('disabled', true);

            var cant_inputs = $("div#inputs_descuentos div.row").length;

            var arrData = [];

            for (var i = 1; i <= cant_inputs; i++) {
                arrData.push([
                    $("#id_descuento_" + i).val(),       //0
                    $("#fecha_descuento_" + i).val(),   //1
                    $("#cantidad_" + i).val(),         //2
                    $("#id_empleado_" + i).val(),     //3
                    $("#descripcion_"+ i).val(),     //4
                    $("#nombre_"+ i).val(),         //5
                    $("#id_concepto_"+ i+" option:selected").val(),   //6
                    $("#id_empleado_" + i+" option:selected").text() //7
                ]);
            }

            iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');
            $.ajax({
                method: 'POST',
                url: '{{route('store.otros_descuentos')}}',
                data: {
                    arrData      : arrData,
                    id_descuento : $("#id_descuento").val()
                },
                success: function (response) {

                    $("#btn_store_descuentos").attr('disabled', false);

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

    function delete_descuento(id_descuento) {
        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea eliminar este descuento?",
            callback: function (result) {
                if (result) {
                    $.ajax({
                        type: 'POST',
                        url: '{{route('delete.otros_descuentos')}}',
                        data: {
                            id_descuento: id_descuento
                        },
                        success: function (response) {
                            open_modal_message('modal_body_message', 'modal_message', response);
                            setTimeout(function(){ location.reload(); },1000)
                        }
                    });
                }
            }
        });
    }

    function set_nombre_descuento(id_input){
        $("#nombre_"+id_input).val($("#id_concepto_"+id_input+" option:selected").text())
    }

    function set_concepto_descuento(id_input,id_select,tipo_descuento){

        $.ajax({
            method: 'GET',
            url: '{{route('get.concepto_descuentos')}}',
            data: {
                id_empleado : $("#"+id_input).val()
            },
            success: function (response) {
                console.log(response)
               $("#"+id_select+ " option.option_dinamic" ).remove()

               $.each(response, (i,j)=>{
                    $("#"+id_select).append(`<option class="option_dinamic" value="${j.invoice_item_type_id}">${j.description}</option>`)
               })

               console.log(tipo_descuento)
               if(typeof tipo_descuento!='undefined')
                $("#"+id_select+" option[value='"+tipo_descuento+"']").attr('selected',true);
            }
        });
    }

</script>
