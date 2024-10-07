<script>

    estadisticas_nomina();

    function estadisticas_nomina(){

        $.ajax({
            method   : 'GET',
            url      : '{{in_array("ADMIN",session("dataUsuario")["user_type"]) ? route('vista.estadisticas_nomina') : route('vista.nomia_empleado')}}',
            data    :{
                fecha       :  $("#fecha").val(),
                id_empleado : '{{session('dataUsuario')['id_empleado']}}'
            },
            success: function (response) {

                var anno = moment().format("YYYY-MM-DD");
                if(response[1] != '')
                     anno = response[1];

                $("#anno").html(anno);

                $("#fecha option#option_dinamic").remove();
                $.each(response[2],function (i,j) {
                    $("#fecha").append('<option id="option_dinamic" value="'+j.anno+'"> Año '+j.anno+' </option>')
                });

                var areaChartData = {
                    labels  : ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio','Agosto','Septiembre','Ocutbre','Noviembre','Diciembre'],
                    datasets: [
                        {
                            label               : 'Electronics',
                            fillColor           : 'rgba(210, 214, 222, 1)',
                            strokeColor         : 'rgba(210, 214, 222, 1)',
                            pointColor          : 'rgba(210, 214, 222, 1)',
                            pointStrokeColor    : '#c1c7d1',
                            pointHighlightFill  : '#fff',
                            pointHighlightStroke: 'rgba(220,220,220,1)',
                            data                : response[0]
                        }

                    ]
                };

                var barChartCanvas                   = $('#barChart').get(0).getContext('2d');
                var barChart                         = new Chart(barChartCanvas);
                var barChartData                     = areaChartData;
                barChartData.datasets[0].fillColor   = '#00a65a';
                barChartData.datasets[0].strokeColor = '#00a65a';
                barChartData.datasets[0].pointColor  = '#00a65a';

                var barChartOptions                  = {
                    //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
                    scaleBeginAtZero: true,
                    //Boolean - Whether grid lines are shown across the chart
                    scaleShowGridLines: true,
                    //String - Colour of the grid lines
                    scaleGridLineColor: 'rgba(0,0,0,.05)',
                    //Number - Width of the grid lines
                    scaleGridLineWidth: 1,
                    //Boolean - Whether to show horizontal lines (except X axis)
                    scaleShowHorizontalLines: true,
                    //Boolean - Whether to show vertical lines (except Y axis)
                    scaleShowVerticalLines: true,
                    //Boolean - If there is a stroke on each bar
                    barShowStroke: true,
                    //Number - Pixel width of the bar stroke
                    barStrokeWidth: 2,
                    //Number - Spacing between each of the X value sets
                    barValueSpacing: 5,
                    //Number - Spacing between data sets within X values
                    barDatasetSpacing: 1,

                    //Boolean - whether to make the chart responsive
                    responsive              : true,
                    maintainAspectRatio     : true
                };
                barChartOptions.datasetFill = false;
                barChart.Bar(barChartData, barChartOptions);
            }
        });

    }
</script>
