<script>
    function add_consumo(invoice_id,id_empleado){
        $.ajax({
            method   : 'GET',
            url      : '{{route('form.admin_consumos')}}',
            data     : {
                invoice_id : invoice_id,
                id_empleado : id_empleado
            },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-lg');
            }
        });
    }


    function store_consumo(invoice_id) {

        $("#msg").html("");
        total = $("#total").val();
        pagado = $("#pagado").val();
        total_descuento = total-pagado;

        if($("#a_pagar").val() > total_descuento){
            $("#msg").html("Sólo puede descontar la cantidad de "+ "$"+total_descuento.toFixed(2));
        }else{
            $("#msg").html("");
            if ($('#form_consumo').valid()) {
                $.ajax({
                    method: 'POST',
                    url: '{{route('store.admin_consumo')}}',
                    data: {
                        invoice_id : invoice_id,
                        fecha_descuento : $("#fecha_descuento").val(),
                        a_pagar : $("#a_pagar").val(),
                        id_consumo: $("#id_consumo").val(),
                        id_empleado : $("#id_empleado_consumo").val()
                    },
                    success: function (response) {
                        open_modal_message('modal_body_message', 'modal_message', response.msg);
                    }
                });
            }
        }
    }
    
    function edit_consumo() {
        
    }

</script>