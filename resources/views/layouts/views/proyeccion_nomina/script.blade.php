<script>

    $('.Date').datepicker({
        format: 'mm',
        //endDate: '0d',
        //startDate: '0d',
        language: 'es-ES'
    });

    function proyectar() {
        if($('#form_fechas').valid()){
            load(1);
            arrEmpleados = [];
            $.each($('input:checkbox[name=empleado]:checked'), function (i, j) {
                arrEmpleados.push({ empleado : j.value });
            });

            if(arrEmpleados.length < 1){
                open_modal_message(
                    "modal_body_message","modal_message","<div class='alert alert-danger' role='alert' style='margin: 0'>"+
                    "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i>"+
                    " Debe seleccionar al menos 1 empleado para generar proyección de nómina.!"+
                    "</div>");
                return false;
            }

            $.ajax({
                method   : 'GET',
                url      : '{{route('proyeccion-nomina.show',0)}}',
                data     : {
                    anno : $("#anno").val(),
                    fecha_inicio : $("#fecha_inicio_calculo").val(),
                    fecha_fin    : $("#fecha_fin_calculo").val(),
                    arrEmpleados : arrEmpleados
                },
                success: function (response) {
                    load(0);
                    $("#li_proyeccion, #a_proyeccion").removeClass("active");
                    $("#li_proyeccion_empleado, #a_proyeccion_programacion").addClass("active");
                    $("#li_proyeccion_empleado").removeClass("noclick");
                    $("#a_proyeccion_programacion").html(response);
                }
            });

        }
    }

    function form_proyeccion() {
        load(1);
        $.ajax({
            method   : 'GET',
            url      : '{{route('form-proyeccion')}}',
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-xs');
                load(0);
            }
        });
    }

    function store_programacion() {
        if ($('#form_add_programacion').valid()) {
            load(1);
            iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');
            var formData = new FormData($("#form_add_programacion")[0]);
            $.ajax({
                method: 'POST',
                url: '{{route('proyeccion-nomina.store')}}',
                data   : formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    open_modal_message('modal_body_message', 'modal_message', response.msg);
                    load(0);
                }
            });
        }
    }

    function descargar_programacion() {
        load(1);
        arrEmpleado = [];
        $.each($(".empleado"),function (i,j) {
            if($("#empleado_"+(i+1)).is(":checked"))
                arrEmpleado.push( j.value );
        });

        $.ajax({
            method   : 'GET',
            url      : '{{route('proyeccion-nomina.create')}}',
            data :{
                fecha_inicio_calculo : $("#fecha_inicio_calculo").val(),
                fecha_fin_calculo : $("#fecha_fin_calculo").val(),
                arrEmpleado : arrEmpleado
            },
            success: function (response) {
                var a = document.createElement("a");
                a.href = response.file;
                a.download = response.name;
                document.body.appendChild(a);
                a.click();
                a.remove();
                load(0);
            }
        });
    }

    function store_proyeccion(){
        if ($('#form_add_programacion').valid()) {
            load(1);
            var formData = new FormData($("#form_add_programacion")[0]);

            $.ajax({
                method: 'POST',
                url: '{{route('proyeccion-nomina.store')}}',
                data   : formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    load(0);
                    $("#btn_contrataciones").attr('disabled',false);
                    open_modal_message('modal_body_message', 'modal_message', response.msg);
                }
            });
        }
    }

    function calcular(input) {

    }

</script>