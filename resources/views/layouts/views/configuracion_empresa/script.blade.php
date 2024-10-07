<script>
    function store_configuracion() {

        if($('#form_configuracion').valid()){

            iconAccion('ico','fa-floppy-o','fa-spinner fa-pulse fa-fw');
            var formData = new FormData($("#form_configuracion")[0]);
            formData.append('descrip_empresa',CKEDITOR.instances['descrip_empresa'].getData());

            $.ajax({
                method : 'POST',
                url    : '{{route('configuracion-empresa.store')}}',
                data   : formData,
                processData: false,
                contentType: false,

                success:function (response) {

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

    function store_configuracion_varibles() {

        if($('#form_configuracion_variables').valid()){

            iconAccion('ico2','fa-floppy-o','fa-spinner fa-pulse fa-fw');

            $.ajax({
                method : 'POST',
                url    : '{{route('configuracion-empresa-variables.store')}}',
                data   : {
                    entre_semana_relacion_dependencia : $("#entre_semana_relacion_dependencia").val(),
                    fin_semana_relacion_dependencia   : $("#fin_semana_relacion_dependencia").val(),
                    entre_semana                      : $("#entre_semana").val(),
                    fin_semana                        : $("#fin_semana").val(),
                    id_configuracion_variables        : $("#id_configuracion_variables").val(),
                    sbuv                              : $("#sbuv").val(),
                    vacaciones_dias_entre_semana      : $("#vacaciones_dias_entre_semana").val(),
                    vacaciones_dias_fines_semana      : $("#vacaciones_dias_fines_semana").val(),
                    porcentaje_avance                 : $("#porcentaje_avance").val(),
                    diferir_consumos_meses            : $("#diferir_consumos_meses").val(),
                    iva                               : $("#iva").val(),
                    aporte_patronal                   : $("#aporte_patronal").val(),
                    aporte_personal                   : $("#aporte_personal").val(),
                    fondo_reserva                     : $("#fondo_reserva").val(),
                    anno_calculo_fondo_reserva        : $("#anno_calculo_fondo_reserva").val(),
                    antiguedad                        : $("#antiguedad").val(),
                    fecha_hasta                       : $("#fecha_hasta").val(),
                    intervalo                         : $("#intervalo").val(),
                    tiempo_carga_he                   : $("#tiempo_carga_he").val(),
                    tiempo_aprov_he                   : $("#tiempo_aprov_he").val(),
                },
                success:function (response) {

                    if(response.status == 1){
                        iconAccion('ico2','fa-spinner fa-pulse fa-fw','fa-check');
                    }else{
                        iconAccion('ico2','fa-spinner fa-pulse fa-fw','fa-times');
                    }
                    open_modal_message('modal_body_message','modal_message',response.msg);
                }
            });
        }
    }

</script>
