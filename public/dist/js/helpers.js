$(document).ajaxStart(function() { Pace.restart(); });
$('#flash-overlay-modal').modal();

function open_modal_form(id_body_modal,id_modal,response,class_width) {
    $("#width-modal").addClass(class_width);
    $("#"+id_body_modal).html(response);
    $("#"+id_modal).modal('show');
}

function open_modal_message(id_body_modal,id_modal,response,reload) {
    $("#"+id_body_modal).html(response);
    $("#"+id_modal).modal('show');
    if(reload !== undefined)
        $("#"+id_modal).on('hidden.bs.modal', function () { location.reload(); });

}

function msg_error(msg) {
    return '<span class="text-danger">'+msg+'</span>'
}

function iconAccion(id_element_add,remove_icon,add_icon){
    $("#"+id_element_add).removeClass(remove_icon);
    $("#"+id_element_add).addClass(add_icon);
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    jQuery.extend(jQuery.validator.messages, {
        required: "Este campo es obligatorio.",
        remote: "Por favor, rellena este campo.",
        email: "Por favor, escribe una dirección de correo válida",
        url: "Por favor, escribe una URL válida.",
        date: "Por favor, escribe una fecha válida.",
        dateISO: "Por favor, escribe una fecha (ISO) válida.",
        number: "Por favor, escribe un número entero válido.",
        digits: "Por favor, escribe sólo dígitos.",
        creditcard: "Por favor, escribe un número de tarjeta válido.",
        equalTo: "Por favor, escribe el mismo valor de nuevo.",
        accept: "Por favor, escribe un valor con una extensión aceptada.",
        maxlength: jQuery.validator.format("Sólo se permite hasta {0} caracteres."),
        minlength: jQuery.validator.format("Escribe almenos de {0} caracteres."),
        rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."),
        range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."),
        max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."),
        min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.")
    });


});


function filterFloat(evt,input){
    // Backspace = 8, Enter = 13, ‘0′ = 48, ‘9′ = 57, ‘.’ = 46, ‘-’ = 43
    var key = window.Event ? evt.which : evt.keyCode;
    var chark = String.fromCharCode(key);
    var tempValue = input.value+chark;
    if(key >= 48 && key <= 57){
        return filter(tempValue) !== false;
    }else{
        if(key == 8 || key == 13 || key == 0) {
            return true;
        }else if(key == 46){
            return filter(tempValue) !== false;
        }else{
            return false;
        }
    }
}

function filter(__val__){
    var preg = /^([0-9]+\.?[0-9]{0,4})$/;
    if(preg.test(__val__) === true){
        return true;
    }else{
        return false;
    }

}

$('[data-toggle="tooltip"]').tooltip();

$('[data-toggle="popover"]').popover();

function init_events(ele) {
    ele.each(function () {

        // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
        // it doesn't need to have a start or end
        var eventObject = {
            title: $.trim($(this).text()) // use the element's text as the event title
        };

        // store the Event Object in the DOM element so we can get to it later
        $(this).data('eventObject', eventObject);

        // make the event draggable using jQuery UI
        $(this).draggable({
            zIndex: 1070,
            revert: true, // will cause the event to go back to its
            revertDuration: 0  //  original position after the drag
        })
    })
}

function form_nivelar_nomina() {

    $.ajax({
        method   : 'GET',
        url      : 'form-nivelar-nomina',
        success: function (response) {
            open_modal_form('body_modal','modal-default',response,'modal-lg');
        }
    });
}

function nivelar_nomina() {
    if ($('#form_nivelar_nomina').valid()) {
        load(1);
        iconAccion('ico', 'fa-floppy-o', 'fa-spinner fa-pulse fa-fw');
        $("#btn_nivelar_nomina").attr('disabled', true);
        arrVacaciones = [];
        arrDecimoTercero = [];
        $.each($('input[name=fecha_ultimas_vacaciones]'), function (i, j) {
            arrVacaciones.push([j.id, j.value]);
        });
        $.each($('input[name=decimo_tercero]'), function (i, j) {
            arrDecimoTercero.push([j.id, j.value]);
        });
        $.ajax({
            method: 'POST',
            url: 'nivelar-nomina',
            data: {
                arrVacaciones: arrVacaciones,
                arrDecimoTercero : arrDecimoTercero
            },
            success: function (response) {
                open_modal_form('body_modal', 'modal-default', response, 'modal-lg');
                load(0);
            }
        });

    }
}

function load(carga) {
    carga === 1 ? $(".loader").fadeIn("slow") : $(".loader").fadeOut("slow");
}



