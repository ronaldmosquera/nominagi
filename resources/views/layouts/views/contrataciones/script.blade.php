<script>

    @if(!isset($dataContratacion)) campos_obligatorios(); @endif

    function campos_obligatorios() {

        $.ajax({
            method: 'GET',
            url: '{{route('vista.campos_obligatorios')}}',
            data: {
                _token : '{{ csrf_token() }}',
                id_tipo_contrato : $("#id_tipo_contrato").val()
            },
            success: function (response) {
                $("#campos_obligatorios").html(response);
            }
        });
    }

    function campos_relacion_dependecia() {
        $.ajax({
            method: 'GET',
            url: '{{route('vista.relacion_dependencia')}}',
            data: {
                _token : '{{ csrf_token() }}',
            },
            success: function (response) {

                $("#campos_relacion_dependencia").html(response);
            }
        });
    }

    function campos_sin_relacion_dependencia(id_detalle_contratacion) {
        $.ajax({
            method: 'GET',
            url: '{{route('vista.relacion_sin_dependencia')}}',
            data: {
                id_detalle_contratacion : id_detalle_contratacion
            },
            success: function (response) {

                setTimeout(function () {
                    $("div.sin_relacion_dependencia").append(response);
                },500)

            }
        });
    }

    function cuerpo_contrato() {

        $.ajax({
            method: 'GET',
            url: '{{route('contrataciones.show',0)}}',
            data: {
                _token           : '{{ csrf_token() }}',
                id_tipo_contrato : $("#id_tipo_contrato").val()
            },
            success: function (response) {

                //$("#salario").attr("min",response.sueldo_minimo);

                CKEDITOR.instances['body_contrato'].setReadOnly(true);
                CKEDITOR.instances['body_contrato'].setData(response.body);

                if(response.tipo_contrato_descripcion == 1){
                    $("#div_salario,#fecha_cargo").remove();
                    $("div.sin_relacion_dependencia div").remove();
                    $("div.sin_relacion_dependencia div").remove();
                }else{
                    campos_obligatorios();
                }

                if(response.relacion_dependencia){
                    $("div.sin_relacion_depencdencia div").remove();
                    campos_relacion_dependecia();
                }else{
                    $("#div_campos_relacion_dependencia").remove();
                    if(response.tipo_contrato_descripcion == 2){
                        campos_sin_relacion_dependencia();
                    }else if(response.tipo_contrato_descripcion == 1){
                        $("div.sin_relacion_dependencia div").remove();
                    }
                }

                $("option#dinamic").remove();
                for(var i=0; i<response.dataEmpleados.length; i++){
                    $("#id_empleado").append("<option id='dinamic' value="+response.dataEmpleados[i]['id_empleado']+">"+response.dataEmpleados[i]['nombre']+" </option>");
                }
            }
        });
    }

    /*function contrato_estandar() {

        $("#estandar").is(':checked')
            ?  CKEDITOR.instances['body_contrato'].setReadOnly(true)
            : CKEDITOR.instances['body_contrato'].setReadOnly(false);
    }*/

    function store_contratacion() {
        if ($('#form_add_contratacion').valid()) {
            load(1);
            iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');
            var formData = new FormData($("#form_add_contratacion")[0]);
            formData.append('letras',$("#letras").val());

            $("#btn_contrataciones").attr('disabled',true);
            $.ajax({
                method: 'POST',
                url: '{{route('contrataciones.store')}}',
                data   : formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $("#btn_contrataciones").attr('disabled',false);
                    if (response.status == 1) {
                        iconAccion('ico', 'fa-spinner fa-pulse fa-fw', 'fa-check');
                    } else {
                        iconAccion('ico', 'fa-spinner fa-pulse fa-fw', 'fa-times');
                    }
                    load(0);
                    open_modal_message('modal_body_message', 'modal_message', response.msg);
                }
            });
        }
    }

    function tipo_usuario() {

        if($("#tipo_empleado").is(':checked')){
            $("#select_empleados").toggle('hide');
            $.ajax({
                method: 'POST',
                url: '{{route('vista.inputs_empleado')}}',
                data: {
                    _token : '{{ csrf_token() }}',
                },
                success: function (response) {
                    $("#datos_empleados").html(response);
                    $("#datos_empleados").removeClass('hide');
                    $("#div_contrato").removeClass('col-md-4');
                    $("#div_contrato").addClass('col-md-6');
                    $("#div_salario").removeClass('col-md-4');
                    $("#div_salario").addClass('col-md-6');
                }
            });
        }else{
            $("#select_empleados").toggle('show');
            $("#datos_empleados").addClass('hide');
            $("#datos_empleados").html('');
            $("#div_contrato").removeClass('col-md-6');
            $("#div_contrato").addClass('col-md-4');
            $("#div_salario").removeClass('col-md-6');
            $("#div_salario").addClass('col-md-4');
        }

    }

    function store_terminar_contratatacion(id_contrataciones,message,id_motivo_anulacion) {
        if(message == 1){
            bootbox.confirm({
                size: null,
                message: "¿Esta seguro que desea terminar este contrato?",
                callback: function (result) {
                    if (result) {
                        iconAccion('cog', 'fa-cog', 'fa-cog fa-spin fa-fw');
                        peticion(id_contrataciones,1,id_motivo_anulacion);
                        iconAccion('cog', 'fa-cog fa-spin fa-fw', 'fa-cog');
                    }
                }
            });
        }else{
            if ($('#form_anulacion_contrato').valid()) {
                peticion(id_contrataciones,0,id_motivo_anulacion);
            }
        }


    }

    function peticion(id_contrataciones,store,id_motivo_anulacion) {

        load(1)
        let tabla = $("#tabla_datos_liquidacion")
        let objMontos ={}

        if(store == 1){

            objMontos={
                bono25: tabla.find('td#bono25 input').length ? tabla.find('td#bono25 input').val() : (tabla.find('td#bono25').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#bono25').html().trim())),
                vistoBueno: tabla.find('td#vistoBueno input').length ? tabla.find('td#vistoBueno input').val() : (tabla.find('td#vistoBueno').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#vistoBueno').html().trim())),
                despidoIneficaz: tabla.find('td#despidoIneficaz input').length ? tabla.find('td#despidoIneficaz input').val() : (tabla.find('td#despidoIneficaz').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#despidoIneficaz').html().trim())),
                indemnizacionDiscapacidad: tabla.find('td#indemnizacionDiscapacidad input').length ? tabla.find('td#indemnizacionDiscapacidad input').val() : (tabla.find('td#indemnizacionDiscapacidad').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#indemnizacionDiscapacidad').html().trim())),
                montoDecimoTercerSueldo: tabla.find('td#montoDecimoTercerSueldo input').length ? tabla.find('td#montoDecimoTercerSueldo input').val() : (tabla.find('td#montoDecimoTercerSueldo').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#montoDecimoTercerSueldo').html().trim())),
                montoDecimoCuartoSueldo: tabla.find('td#montoDecimoCuartoSueldo input').length ? tabla.find('td#montoDecimoCuartoSueldo input').val() : (tabla.find('td#montoDecimoCuartoSueldo').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#montoDecimoCuartoSueldo').html().trim())),
                montoVacaciones: tabla.find('td#montoVacaciones input').length ? tabla.find('td#montoVacaciones input').val() : (tabla.find('td#montoVacaciones').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#montoVacaciones').html().trim())),
                montoDesahucio: tabla.find('td#montoDesahucio input').length ? tabla.find('td#montoDesahucio input').val() : (tabla.find('td#montoDesahucio').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#montoDesahucio').html().trim())),
                montoDespidoIntempestivo: tabla.find('td#montoDespidoIntempestivo input').length ? tabla.find('td#montoDespidoIntempestivo input').val() : (tabla.find('td#montoDespidoIntempestivo').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#montoDespidoIntempestivo').html().trim())),
                montoHorasExtras: tabla.find('td#montoHorasExtras input').length ? tabla.find('td#montoHorasExtras input').val() : (tabla.find('td#montoHorasExtras').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#montoHorasExtras').html().trim())),
                montoComisiones: tabla.find('td#montoComisiones input').length ? tabla.find('td#montoComisiones input').val() : (tabla.find('td#montoComisiones').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#montoComisiones').html().trim())),
                montoConsumos: tabla.find('td#montoConsumos input').length ? tabla.find('td#montoConsumos input').val() : (tabla.find('td#montoConsumos').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#montoConsumos').html().trim())),
                montoDescuentos: tabla.find('td#montoDescuentos input').length ? tabla.find('td#montoDescuentos input').val() : (tabla.find('td#montoDescuentos').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#montoDescuentos').html().trim())),
                aportePersonal: tabla.find('td#aportePersonal input').length ? tabla.find('td#aportePersonal input').val() : (tabla.find('td#aportePersonal').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#aportePersonal').html().trim())),
                montoAnticipos: tabla.find('td#montoAnticipos input').length ? tabla.find('td#montoAnticipos input').val() : (tabla.find('td#montoAnticipos').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#montoAnticipos').html().trim())),
                iva: tabla.find('td#iva input').length ? tabla.find('td#iva input').val() : (tabla.find('td#iva').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#iva').html().trim())),
                retencionIva: tabla.find('td#retencionIva input').length ? tabla.find('td#retencionIva input').val() : (tabla.find('td#retencionIva').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#retencionIva').html().trim())),
                retencionRenta: tabla.find('td#retencionRenta input').length ? tabla.find('td#retencionRenta input').val() : (tabla.find('td#retencionRenta').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#retencionRenta').html().trim())),
                montoSalario: tabla.find('td#montoSalario input').length ? tabla.find('td#montoSalario input').val() : (tabla.find('td#montoSalario').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#montoSalario').html().trim())),
                /* montoTotalARecibir: tabla.find('td#montoTotalARecibir input').length ? tabla.find('td#montoTotalARecibir input').val() : (tabla.find('td#montoTotalARecibir').html().trim() == 'N/A' ? 0 : parseFloat(tabla.find('td#montoTotalARecibir').html().trim())) */
            }

        }

        $.ajax({
            method: 'POST',
            url: '{{route('terminar-contratacion.store')}}',
            data   : {
                _token             : '{{ csrf_token() }}',
                id_contrataciones  : id_contrataciones,
                id_motivo_anulacion: $("#id_motivo_anulacion").val() == undefined ? id_motivo_anulacion : $("#id_motivo_anulacion").val(),
                store              : store,
                fecha_terminacion : $("#fecha_terminacion").val(),
                visto_bueno : $("#despido_visto_bueno").is(':checked'),
                indemnizacion_discapacidad : $("#indemnizacion_discapacidad").is(':checked'),
                terminación_antes_plazo : $("#indemnizacion_terminación_antes_plazo").is(':checked'),
                despido_ineficaz : $("#despido_ineficaz").is(':checked'),
                bono_25_porciento : $("#bono_25_porciento").is(':checked'),
                ...objMontos
            },
            success: function (response) {
                if(response.msg)
                    open_modal_message('modal_body_message', 'modal_message', response.msg);
                else
                    open_modal_form('body_modal','modal-default',response,'modal-xs');

                load(0);
            }
        });
    }

    function add_contrato_firmado(id_contrataciones) {

        $.ajax({
            method : 'POST',
            url    : '{{route('vista.imagen-contratacion')}}',
            data   :{
                _token      : '{{ csrf_token() }}',
                id_contrataciones : id_contrataciones
            },
            success: function (response) {

                open_modal_form('body_modal','modal-default',response,'modal-xs');
            }
        });

    }

    function upload_contratacion_firmada() {
        if ($('#form_add_imagen_contrataciones').valid()) {

            var formData = new FormData($("#form_add_imagen_contrataciones")[0]);
            formData.append('id_contratacion',$("#idContratacion").val());

            bootbox.confirm({
                size: null,
                message: "¿Esta seguro que desea subir estas imágenes a esta contratación?",
                callback: function (result) {
                    if (result) {
                        iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');
                        $.ajax({
                            method: 'POST',
                            url: '{{route('upload.imagenes-contratacion')}}',
                            data   : formData,
                            processData: false,
                            contentType: false,
                            success: function (response) {
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
            });
        }
    }

    function formulario(){

        if($("#agregar").is(':checked')){
            $("#form_add_imagen_contrataciones").removeClass('hide');
        }else{
            $("#form_add_imagen_contrataciones").addClass('hide');
        }
    }

    function eliminar_imagen_contrataciones(id_imagen,nombre_imagen) {
        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea Eliminar esta imágene de esta contratación?",
            callback: function (result) {
                if (result) {
                    iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');
                    $.ajax({
                        method: 'POST',
                        url: '{{route('delete.imagenes-contratacion')}}',
                        data   : {
                            id_imagen     : id_imagen,
                            nombre_imagen : nombre_imagen
                        },
                        success: function (response) {
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
        });
    }

    function anular_contrato(id_contrato) {
        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea terminar este contrato?",
            callback: function (result) {
                if (result) {
                    $.ajax({
                        method: 'GET',
                        url: '{{route('anular_contratacion.store')}}',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id_contrato: id_contrato
                        },
                        success: function (response) {
                            open_modal_form('body_modal', 'modal-default', response.msg, 'modal-xs');
                        }
                    });
                }
            }
        });
    }

    function terminar_contrato(id_contrato) {
        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea terminar este contrato?, al realizar esta acción se tomará en cuenta para todos los " +
                "calculos correspondiente a la liquidación a la que hubiere lugar hasta la fecha seleccionada!",
            callback: function (result) {
                if (result) {
                    $.ajax({
                        method : 'GET',
                        url    : '{{route('vista.form_terminacion_contrato')}}',
                        data   :{
                            _token      : '{{ csrf_token() }}',
                            id_contrato : id_contrato
                        },
                        success: function (response) {
                            open_modal_form('body_modal','modal-default',response,'modal-lg');
                        }
                    });
                }
            }
        });
    }

    function update_contratacion() {
        if ($('#form_add_contratacion').valid()) {
            load(1);
            iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');
            var formData = new FormData($("#form_add_contratacion")[0]);
            formData.append('id_contratacion',$("#id_contratacion").val());
            formData.append('party_id',$("#party_id").val());
            formData.append('contact_mech_id',$("#contact_mech_id").val());
            formData.append('party_id_contact',$("#party_id_contact").val());
            formData.append('id_detalle_contrataciones',$("#id_detalle_contrataciones").val());
            formData.append('id_tipo_contrato',$("#id_tipo_contrato").val());
            formData.append('letras',$("#letras").val());

            $("#btn_contrataciones").attr('disabled',true);
            $.ajax({
                method: 'POST',
                url: '{{action('ContratacionesController@updateContratacion')}}',
                data   : formData,
                processData: false,
                contentType: false,
                success: function (response) {

                    $("#btn_contrataciones").attr('disabled',false);
                    if (response.status == 1) {
                        iconAccion('ico', 'fa-spinner fa-pulse fa-fw', 'fa-check');
                    } else {
                        iconAccion('ico', 'fa-spinner fa-pulse fa-fw', 'fa-times');
                    }
                    load(0);
                    open_modal_message('modal_body_message', 'modal_message', response.msg);
                }
            });
        }
    }

    $("#salario").keyup(function() {

        if($("#salario").val().length == 0 )
            $("#letras").val("");

        if($("#salario").val().length > 1){
            $.ajax({
                method : 'GET',
                url    : '{{url('numero-letras')}}',
                data   :{
                    cadena : $("#salario").val()
                },
                success: function (response) {
                    $("#letras").val(response.trim());
                }
            });
        }
    });

    function validacionSueldoMinimo() {

        $.ajax({
            method : 'GET',
            url    : '{{url('validacion.sueldo_minimo')}}',
            data   :{
                id_tipo_contrato : $("#id_tipo_contrato").val()
            },
            success: function (response) {
                $("#letras").val(response.trim());
            }
        });
    }

    converitr_letras();

    function converitr_letras(sueldo_sectorial,input_value) {

        if(sueldo_sectorial > 0){
            if(parseFloat(input_value) < parseFloat(sueldo_sectorial)){
                $(".salario label#salario-error").remove();
                $(".salario").append('<label id="salario-error" class="error" for="salario">El salario debe ser mínimo de '+sueldo_sectorial+' Dolares.</label>');
                //$("#salario").val(sueldo_minimo);
            }else{
                $(".salario label#salario-error").remove();
            }
        }

        if($("#salario").val().length == 0 )
            $("#letras").val("");

        if($("#salario").val().length > 0){
            setTimeout(function () {
                $.ajax({
                    method : 'GET',
                    url    : '{{url('numero-letras')}}',
                    data   :{
                        cadena : $("#salario").val()
                    },
                    success: function (response) {
                        console.log($("#salario").val(),response.trim());
                        $("#letras").val(response.trim());
                    }
                });
            },200)
        }
    }

    function documentos_firmados() {
        $.ajax({
            method : 'GET',
            url    : '{{route('vista.imagen_contratacion_firamda_empleado')}}',
            data   :{
                id_empleado : '{{session('dataUsuario')['id_empleado']}}'
            },
            success: function (response) {
                open_modal_message('modal_body_message', 'modal_message', response);
            }
        });

    }

    function form_addendum_contratatacion(id_contratacion) {
        $.ajax({
            method: 'GET',
            url: '{{route('vista.add_addemdun')}}',
            data: {
                _token : '{{ csrf_token() }}',
                id_contratacion: id_contratacion
            },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-lg');
            }
        });
    }

    function store_addendum(salario,horas_laborales,cargo,iva,retencion_iva,retencion_renta) {

        //console.log($("#iva").val(),$("#retencion_iva").val(),$("#retencion_renta").val());
        //return false;
        if(salario === $("#salario").val() && horas_laborales === $("#horas").val() && cargo === $("#id_cargo").val() && CKEDITOR.instances['cuerpo_adendum'].getData().length === 0 && iva === $("#iva").val() && retencion_iva == $("#retencion_iva").val() && retencion_renta === $("#retencion_renta").val()){
            open_modal_message('modal_body_message', 'modal_message',
                '<div class="alert alert-danger" role="alert" style="margin: 0">'+
                        ' Debe hacer algún cambio en los atributos de la antigua contraración para generar un addendum '+
                ' </div>');
            return false;
        }else{
            if(CKEDITOR.instances['cuerpo_adendum'].getData().length < 1){
                $(".cuerpo label#horas-error").remove();
                $(".cuerpo").append("<label id='horas-error' style='position: initial;' class='error'>Debe comentar el cambio realizado en el área Cuerpo del addendum</label>");
                return false;
            }
        }

        if ($('#addendum_contratacion').valid()) {

            bootbox.confirm({
                size: null,
                message: "¿Esta seguro que desea crear este addendum?",
                callback: function (result) {
                    if (result) {
                        iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');

                        var formData = new FormData($("#addendum_contratacion")[0]);
                        formData.append('letras', $("#letras").val());
                        formData.append('cuerpo_addendum', CKEDITOR.instances['cuerpo_adendum'].getData());
                        formData.append('id_detalle_contratacion', $("#id_detalle_contratacion").val());
                        formData.append('iva', $("#iva").val());
                        formData.append('retencion_iva', $("#retencion_iva").val());
                        formData.append('retencion_renta', $("#retencion_renta").val());

                        $("#btn_contrataciones").attr('disabled', true);
                        $.ajax({
                            method: 'POST',
                            url: '{{route('store.addendum_contrataciones')}}',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                $("#btn_contrataciones").attr('disabled', false);
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
            });
        }
    }

    function valida_sueldo_sectorial() {

        $.ajax({
            method : 'GET',
            url    : '{{route('valida-sueldo-sectorial')}}',
            data   :{
                id_cargo : $("#id_cargo").val(),
                id_tipo_contrato : $("#id_tipo_contrato").val()
            },
            success: function (response) {
                converitr_letras(response.sueldo_minimo_sectorial,response.sueldo_minimo_sectorial);
                if(response.tipo_contrato){
                    $("#salario").val(response.sueldo_minimo_sectorial);
                    $("#salario").attr('min',response.sueldo_minimo_sectorial);
                }


            }
        });

    }

    function form_bonos_fijos(id_contratacion,id_bono_fijo){

        $.ajax({
            method: 'GET',
            url: '{{route('vista.bonos_fijos')}}',
            data: {
                id_bono_fijo: id_bono_fijo,
                id_contratacion :id_contratacion
            },
            success: function (response) {
                open_modal_form('body_modal','modal-default',response,'modal-lg');
            }
        });
    }

    function store_bono_fijo(id_contratacion) {

        if ($('#form_bono_fijo').valid()) {

            cant_inputs = $("div#inputs_bono_fijo div#inputs").length;
            arrData = [];
            for (var i=1; i<=cant_inputs;i++){
                arrData.push([
                    $("#nombre_bono_fijo_"+i).val(),
                    $("#monto_bono_fijo_"+i).val(),
                    $("#id_bono_fijo_"+i).val(),
                    id_contratacion,
                    $("#fecha_asignacion_"+i).val(),
                    $("#apt_patronal_"+i).val()
                ]);
            }

            bootbox.confirm({
                size: null,
                message: "¿Esta seguro que desea agregar este bono fijo a la contratación?",
                callback: function (result) {
                    if (result) {
                        iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');

                        $("#btn_store_bono_fijo").attr('disabled', true);
                        $.ajax({
                            method: 'POST',
                            url: '{{route('store.bonos_fijos')}}',
                            data: {
                                arrData : arrData,
                            },
                            success: function (response) {
                                $("#btn_store_bono_fijo").attr('disabled', false);
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
            });
        }
    }

    function add_inputs_bono_fijo() {

        $("#btn_add_inputs").attr('disabled',true);
        cant_inputs = $("div#inputs_bono_fijo div#inputs").length;
        $.ajax({
            method: 'GET',
            url: '{{route('vista.inputs_bonos_fijos')}}',
            data: {
                cant_inputs: cant_inputs,
            },
            success: function (response) {
                $("#inputs_bono_fijo").append(response);
                $("#btn_add_inputs").attr('disabled',false);
            }
        });
    }

    function add_inputs_prestamos() {

        $("#btn_add_inputs_prestamos").attr('disabled',true);
        cant_inputs = $("div#inputs_prestamo div.inputs").length;
        $.ajax({
            method: 'GET',
            url: '{{route('vista.inputs_prestamos')}}',
            data: {
                cant_inputs: cant_inputs,
            },
            success: function (response) {
                $("#inputs_prestamo").append(response);
                $("#btn_add_inputs_prestamos").attr('disabled',false);
            }
        });
    }

    function delete_inputs_bono_fijo() {

        cant_inputs = $("div#inputs_bono_fijo div#inputs").length;

        if(cant_inputs<2) return false;

        $("div#inputs_bono_fijo div#inputs").last().remove();
    }

    function delete_inputs_prestamo(id_input,id_prestamo) {

        cant_inputs = $("div#inputs_prestamo div.inputs").length;

        if(cant_inputs===1 && typeof id_prestamo== 'undefined')
            return false;


        if(typeof id_prestamo!= 'undefined'){
            $.ajax({
            method: 'POST',
            url: '{{url('eliminar-prestamo')}}',
            data: {
                _token : '{{ csrf_token() }}',
                id_prestamo : id_prestamo
            },
            success: function (response) {
                open_modal_message('modal_body_message','modal_message',response.msg === undefined ? response : response.msg);
                /* if(response.success)
                    $(".modal").on('hidden.bs.modal', function () {
                        location.reload();
                    }); */
            }
        });
        }

        $("#"+id_input).remove()
        //$("div#inputs_prestamo div#inputs").last().remove();
    }

    function store_prestamo(id_contratacion,persona) {

        if ($('#form_prestamos').valid()) {

            cant_inputs = $("div#inputs_prestamo div.inputs").length;
            arrData = [];
            for (var i=1; i<=cant_inputs;i++){
                arrData.push([
                    $("#nombre_prestamo_"+i).val(),
                    $("#cuota_prestamo_"+i).val(),
                    $("#id_prestamo_"+i).val(),
                    id_contratacion,
                    $("#total_prestamo_"+i).val(),
                    $("#fecha_incio_descuento_"+i).val(),
                    persona,

                ]);
            }



            bootbox.confirm({
                size: null,
                message: "¿Esta seguro que desea agregar este prestamo a la contratación?",
                callback: function (result) {
                    if (result) {
                        iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');
                        $("#btn_store_prestamo").attr('disabled', true);

                        $.ajax({
                            method: 'POST',
                            url: '{{route('store.prestamo')}}',
                            data: {
                                arrData : arrData,
                            },
                            success: function (response) {
                                $("#btn_store_prestamo").attr('disabled', false);
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
            });
        }
    }

    function delete_bono_fijo(id_bono_fijo) {
        bootbox.confirm({
            size: null,
            message: "¿Esta seguro que desea eliminar este bono dijo de la contratación?",
            callback: function (result) {
                if (result) {

                    $("#btn_store_prestamo").attr('disabled', true);

                    $.ajax({
                        method: 'POST',
                        url: '{{route('delete.bono_fijo')}}',
                        data: {
                            id_bono_fijo : id_bono_fijo
                        },
                        success: function (response) {
                            if(response.status == 1) {
                                $("#modal-default").modal('hide');
                            }
                            open_modal_message('modal_body_message', 'modal_message', response.msg);
                        }
                    });
                }
            }
        });
    }

    function update_detalle_contratacion(){
        if ($('#form_contratacion').valid()) {

            bootbox.confirm({
                size: null,
                message: "¿Esta seguro que desea actualizar estos datos?",
                callback: function (result) {
                    if (result) {
                        iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');
                        $("#btn_update_detalle_contratacion").attr('disabled', true);
                        $.ajax({
                            method: 'POST',
                            url: '{{route('update.detalle_contratacion')}}',
                            data: {
                                iva: $("#iva").val(),
                                retencion_iva : $("#retencion_iva").val(),
                                retencion_renta : $("#retencion_renta").val(),
                                id_detalle_contratacion : $("#id_detalle_contratacion").val(),
                                horas_laborales : $("#horas_laborales").val(),
                                decimo_tercero : $("#decimo_tercero").val(),
                                decimo_cuarto : $("#decimo_cuarto").val(),
                                fondo_reserva : $("#fondo_reserva").val(),
                                salario : $("#salario").val(),
                                id_banco : $("#id_banco").val(),
                                tipo_cuenta : $("#tipo_cuenta").val(),
                                numero_cuenta: $("#numero_cuenta").val(),
                                party_id : $("#party_id").val(),
                                payment_method_id: $("#payment_method_id").val(),
                                tipo_documento: $("#tipo_documento").val(),
                                tipo_retencion_renta: $("#tipo_impuesto_renta").val(),
                                tipo_retencion_iva: $("#tipo_impuesto_iva").val()
                            },
                            success: function (response) {
                                $("#btn_update_detalle_contratacion").attr('disabled', false);
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
            });
        }
    }

    function search_datos_faltantes() {
        $.ajax({
            method: 'POST',
            url: '{{route('vista.datos_faltantes')}}',
            data: {
                party_id: $("#id_empleado").val()
            },
            success: function (response) {
                console.log(response)
                $("div.emial,div.tlf,div.nacionalidad,div.provincia,div.calle,div.ciudad").remove();
                $("#campos_relacion_dependencia").append(response.html);
                if(response.datosBancarios != null){
                    $('#numero_centa').val(response.datosBancarios.account_number)
                    $("#id_banco option[value='"+response.datosBancarios.codigo_banco+"']").attr('selected',true);
                    $("#tipo_cuenta option[value='"+response.datosBancarios.account_type+"']").attr('selected',true);
                }else{
                    $('#numero_centa').val('')
                    $("#id_banco option").removeAttr('selected');
                    $("#tipo_cuenta option").removeAttr('selected');
                }
            }
        });
    }

    function eliminar_prestamo(id_prestamo){
        console.log(id_prestamo);
        $.ajax({
            method: 'POST',
            url: '{{url('eliminar-prestamo')}}',
            data: {
                _token : '{{ csrf_token() }}',
                id_prestamo : id_prestamo
            },
            success: function (response) {
                open_modal_message('modal_body_message','modal_message',response.msg === undefined ? response : response.msg);
                if(response.success)
                    $(".modal").on('hidden.bs.modal', function () {
                        location.reload();
                    });
            }
        });
    }

    function form_chash_prestamos(){
        $.ajax({
            method: 'POST',
            url: '{{url('form-cash-management-prestamos')}}',
            data   : {},
            success: function (response) {
                open_modal_form('body_modal','modal-default',response);
            }
        });
    }

    function download_cash_management_prestamos(){

        let pagos =[]
        $.each($('input.check_pago_prestamo'),(i,j) => {
            if($(j).is(':checked')){
                pagos.push({
                    'id_contratacion': j.id
                })
            }
        })

        if(!pagos.length){
            let msg = `<div class='alert alert-error'>Debe seleccionar al menos un empleado para realizar el pago</div>`
            open_modal_message('modal_body_message','modal_message',msg)
            return false
        }

        $.ajax({
            method: 'POST',
            url: '{{url('download-cash-management-prestamos')}}',
            data: {
                referencia : $("#referencia_bancaria").val(),
                contrataciones: pagos
            },
            success: function (response) {
                let url = window.location.protocol+'//'+window.location.hostname

                if(url=='{{env('URL_INNOFARM')}}'){
                    empresa = 'INNOFARM'
                }else if (url=='{{env('URL_INNOCLINICA')}}'){
                    empresa = 'INNOCLINICA'
                }else{
                    empresa = 'SERDIMED'
                }

                let a = document.createElement("a");
                a.href = "data:text/plain;base64," + response;
                a.download = 'cash_managment_prestamos_'+empresa+'.txt';
                a.click();
            }
        });
    }

    function store_referencia_bancaria_prestamo(){

        let pagos =[]
        $.each($('input.check_pago_prestamo'),(i,j) => {
            if($(j).is(':checked')){
                pagos.push({
                    'id_contratacion': j.id
                })
            }
        })

        if(!pagos.length){
            let msg = `<div class='alert alert-error'>Debe seleccionar al menos un empleado para realizar el pago</div>`
            open_modal_message('modal_body_message','modal_message',msg)
            return false
        }

        $.ajax({
            method: 'POST',
            url: '{{url('store-referencia-bancaria-prestamos')}}',
            data: {
                referencia : $("#referencia_bancaria").val(),
                contrataciones: pagos
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

    select_retencion()

    function select_retencion(){

        if($("#tipo_documento").val() == "INVOICE_HONORARIOS"){

            $("#tipo_impuesto_renta").attr('required',true)

        }else{

            $("#tipo_impuesto_renta").removeAttr('required')

        }

    }

    function calcula_monto_a_recibir(){

        let tabla = $("#tabla_datos_liquidacion")
        let bono25= tabla.find('td#bono25 input').length ? tabla.find('td#bono25 input').val() : (tabla.find('td#bono25').html().trim() == 'N/A' ? 0 : tabla.find('td#bono25').html().trim())
        let vistoBueno= tabla.find('td#vistoBueno input').length ? tabla.find('td#vistoBueno input').val() : (tabla.find('td#vistoBueno').html().trim() == 'N/A' ? 0 : tabla.find('td#vistoBueno').html().trim())
        let despidoIneficaz= tabla.find('td#despidoIneficaz input').length ? tabla.find('td#despidoIneficaz input').val() : (tabla.find('td#despidoIneficaz').html().trim() == 'N/A' ? 0 : tabla.find('td#despidoIneficaz').html().trim())
        let indemnizacionDiscapacidad= tabla.find('td#indemnizacionDiscapacidad input').length ? tabla.find('td#indemnizacionDiscapacidad input').val() : (tabla.find('td#indemnizacionDiscapacidad').html().trim() == 'N/A' ? 0 : tabla.find('td#indemnizacionDiscapacidad').html().trim())
        let montoDecimoTercerSueldo= tabla.find('td#montoDecimoTercerSueldo input').length ? tabla.find('td#montoDecimoTercerSueldo input').val() : (tabla.find('td#montoDecimoTercerSueldo').html().trim() == 'N/A' ? 0 : tabla.find('td#montoDecimoTercerSueldo').html().trim())
        let montoDecimoCuartoSueldo= tabla.find('td#montoDecimoCuartoSueldo input').length ? tabla.find('td#montoDecimoCuartoSueldo input').val() : (tabla.find('td#montoDecimoCuartoSueldo').html().trim() == 'N/A' ? 0 : tabla.find('td#montoDecimoCuartoSueldo').html().trim())
        let montoVacaciones= tabla.find('td#montoVacaciones input').length ? tabla.find('td#montoVacaciones input').val() : (tabla.find('td#montoVacaciones').html().trim() == 'N/A' ? 0 : tabla.find('td#montoVacaciones').html().trim())
        let montoDesahucio= tabla.find('td#montoDesahucio input').length ? tabla.find('td#montoDesahucio input').val() : (tabla.find('td#montoDesahucio').html().trim() == 'N/A' ? 0 : tabla.find('td#montoDesahucio').html().trim())
        let montoDespidoIntempestivo= tabla.find('td#montoDespidoIntempestivo input').length ? tabla.find('td#montoDespidoIntempestivo input').val() : (tabla.find('td#montoDespidoIntempestivo').html().trim() == 'N/A' ? 0 : tabla.find('td#montoDespidoIntempestivo').html().trim())
        let montoHorasExtras= tabla.find('td#montoHorasExtras input').length ? tabla.find('td#montoHorasExtras input').val() : (tabla.find('td#montoHorasExtras').html().trim() == 'N/A' ? 0 : tabla.find('td#montoHorasExtras').html().trim())
        let montoComisiones= tabla.find('td#montoComisiones input').length ? tabla.find('td#montoComisiones input').val() : (tabla.find('td#montoComisiones').html().trim() == 'N/A' ? 0 : tabla.find('td#montoComisiones').html().trim())
        let montoConsumos= tabla.find('td#montoConsumos input').length ? tabla.find('td#montoConsumos input').val() : (tabla.find('td#montoConsumos').html().trim() == 'N/A' ? 0 : tabla.find('td#montoConsumos').html().trim())
        let montoDescuentos= tabla.find('td#montoDescuentos input').length ? tabla.find('td#montoDescuentos input').val() : (tabla.find('td#montoDescuentos').html().trim() == 'N/A' ? 0 : tabla.find('td#montoDescuentos').html().trim())
        let aportePersonal= tabla.find('td#aportePersonal input').length ? tabla.find('td#aportePersonal input').val() : (tabla.find('td#aportePersonal').html().trim() == 'N/A' ? 0 : tabla.find('td#aportePersonal').html().trim())
        let montoAnticipos= tabla.find('td#montoAnticipos input').length ? tabla.find('td#montoAnticipos input').val() : (tabla.find('td#montoAnticipos').html().trim() == 'N/A' ? 0 : tabla.find('td#montoAnticipos').html().trim())
        let iva= tabla.find('td#iva input').length ? tabla.find('td#iva input').val() : (tabla.find('td#iva').html().trim() == 'N/A' ? 0 : tabla.find('td#iva').html().trim())
        let retencionIva= tabla.find('td#retencionIva input').length ? tabla.find('td#retencionIva input').val() : (tabla.find('td#retencionIva').html().trim() == 'N/A' ? 0 : tabla.find('td#retencionIva').html().trim())
        let retencionRenta= tabla.find('td#retencionRenta input').length ? tabla.find('td#retencionRenta input').val() : (tabla.find('td#retencionRenta').html().trim() == 'N/A' ? 0 : tabla.find('td#retencionRenta').html().trim())
        let montoSalario= tabla.find('td#montoSalario input').length ? tabla.find('td#montoSalario input').val() : (tabla.find('td#montoSalario').html().trim() == 'N/A' ? 0 : tabla.find('td#montoSalario').html().trim())

        let totalARecibir= parseFloat(montoSalario)+parseFloat(bono25)+parseFloat(vistoBueno)+parseFloat(despidoIneficaz)+parseFloat(indemnizacionDiscapacidad)+parseFloat(montoDecimoTercerSueldo)+parseFloat(montoDecimoCuartoSueldo)+parseFloat(montoVacaciones)+parseFloat(montoDesahucio)+parseFloat(montoDespidoIntempestivo)+parseFloat(montoHorasExtras)+parseFloat(montoComisiones)-parseFloat(montoConsumos)-parseFloat(montoDescuentos)-parseFloat(aportePersonal)-parseFloat(montoAnticipos)+parseFloat(iva-retencionIva)-parseFloat(retencionRenta)

        $.each($("td.bonos_fijos"), (i,j) => {
            totalARecibir+= parseFloat($(j).html().trim().substring(3))
        })

        $.each($("td.prestamos"), (i,j) => {
            totalARecibir-= parseFloat($(j).html().trim().substring(3))
        })

        $("#montoTotalARecibir").html(totalARecibir.toFixed(2))

        if(totalARecibir < 0 || isNaN(parseFloat(totalARecibir))){

            $("span.error-total").remove()
            $("div#btn-cancel-contrato button").hide()
            $("div#btn-cancel-contrato").append('<span class="error error-total"> El total a recibir no puede ser un valor negativo.!</span>')

        }else{

            $("span.error-total").remove()
            $("div#btn-cancel-contrato button").show()

        }

    }

</script>
