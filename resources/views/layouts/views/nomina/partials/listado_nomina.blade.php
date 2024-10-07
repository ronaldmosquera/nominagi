@if(isset($dataVistaNomina) && count($dataVistaNomina) > 0)
    <table class="table table-striped" id="tabla_tipo_contrato">
        <tr>
            <th class="text-center">Empleado</th>
            <th class="text-center">Identifiación</th>
            <th class="text-center">Cargo</th>
            <th class="text-center">Nómina</th>
            <th class="text-center">Total ingresos</th>
            <th class="text-center">Total egresos</th>
            <th class="text-center">Total a recibir</th>
            <th class="text-center">Opciones</th>
        </tr>
        @php $total = 0; @endphp
        @foreach($dataVistaNomina as $dataRolEmpleado)
            <tr>
                <td class="text-center">
                    {{$dataRolEmpleado['nombre_empleado']}}
                </td>
                <td class="text-center">
                    {{$dataRolEmpleado['identificacion']}}
                </td>
                <td class="text-center">
                    {{$dataRolEmpleado['cargo']}}
                </td>
                <td class="text-center">
                    {{getMes(intval(\Carbon\Carbon::parse($fecha)->format('m')))}} del {{\Carbon\Carbon::parse($fecha)->format('Y')}}
                </td>
                <td class="text-center">
                    {{"$".number_format($dataRolEmpleado['ingresos'],2,".","")}}
                </td>
                <td class="text-center">
                    {{"$". number_format($dataRolEmpleado['Egresos'],2,".","")}}
                </td>
                <td class="text-center">
                    {{"$".number_format($dataRolEmpleado['total'],2,".","")}}
                </td>
                <td class="text-center">
                    <a href="{{route('generar.nomina',['id_empleado'=>$dataRolEmpleado['id_empleado'],'store'=>2,'fecha'=>$fecha])}}" target="_blank" class="btn btn-success"
                       data-toggle="tooltip" title="Ver rol">

                        <i class="fa fa-id-card-o" aria-hidden="true"></i>
                    </a>
                </td>
            </tr>
            @php $total += number_format($dataRolEmpleado['total'],2,".",""); @endphp
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-center" style="font-weight: bold; background: #00ca6d;color: white;font-size: 12pt;">Total nómina: {{"$".number_format($total,2,".","")}}</td>
        </tr>
    </table>
@else
    <div class="alert alert-danger col-md-12" role="alert">
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span class="sr-only">Error:</span>
        No se encontraton registros
    </div>
@endif

<script>
@if($aprobar)
    $("#a_aprobar_nomina").css('visibility','visible');
@endif
</script>
