<div id="calendar"></div>
    <script>
        $(function () {

            // initialize the calendar
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month',
                },
                editable: true,
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week : 'Semana',
                    day  : 'Dia',

                },
                customButtons: {
                    botonGuardar: {
                        text: ' Guardar',
                        click: function() {
                            guardar_horarios();
                        }
                    }
                },
                validRange: {
                    //start: moment().format('YYYY-MM-DD'),
                },
                eventRender: function(event, element) {
                    element.attr('title', event.tooltip);
                },
                themeSystem:'bootstrap4',
                themeButtonIcons: true,
                footer : {
                    center: 'botonGuardar',
                },
                monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                //Events
                events    :[
                    @foreach($dataHorario as $horario)
                        {
                            title          : 'Desde {{$horario->desde}} Hasta {{$horario->hasta}}',
                            start          : '{{$horario->fecha}}',
                            className      : '{{$horario->clase}}',
                            tooltip        : 'Desde {{$horario->desde}} Hasta {{$horario->hasta}}'
                        },
                    @endforeach
                ],
                droppable : true, // this allows things to be dropped onto the calendar !!!
                editable: true,
                startEditable : false,
                eventClick: function(event) {
                  $('#calendar').fullCalendar('removeEvents', event._id);
                },
                drop : function (date, allDay,event)
                {
                    var originalEventObject = $(this).data('eventObject');

                    // we need to copy it, so that multiple events don't have a reference to the same object
                    var copiedEventObject = $.extend({}, originalEventObject);

                    // assign it the date that was reported
                    copiedEventObject.start           = date;
                    copiedEventObject.allDay          = allDay;
                    copiedEventObject.backgroundColor = $(this).css('background-color');
                    copiedEventObject.borderColor     = $(this).css('border-color');
                    copiedEventObject.className       = event.helper[0].classList[3];

                    $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
                }
            });

            $(".fc-botonGuardar-button").prepend('<i class="fa fa-floppy-o" aria-hidden="true"></i>');
            $(".fc-botonGuardar-button").addClass('btn-lg, ico_store');
            $(".fc-botonGuardar-button i").attr('id','ico_store');
            $(".fc-botonGuardar-button").css('font-size','13pt');
        });

    </script>