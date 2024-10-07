@extends('layouts.principal')
@section('title')
    Lista de pagos
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Listado de roles de pago</h3>
                    <div class="box-tools">
                        <a href="{{route('nomina.index',["store"=>1])}}" target="_blank"
                           data-toggle="popover"
                           data-trigger="hover" data-placement="bottom" title="Acción"
                           data-content="Al hacer click se guardaran todos los datos correspondientes en la base de datos y se generará la nómina"
                           style="cursor:pointer;" aria-hidden="true" class="btn btn-success">
                            <i class="fa fa-check-square-o" aria-hidden="true"></i>
                            Aprobar nómina
                        </a>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    @if(isset($dataVistaNomina) && count($dataVistaNomina) != 0)
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
                                        {{getMes(intval(\Carbon\Carbon::now()->subMonth(1)->format('m')))}} del {{\Carbon\Carbon::now()->format('m') == 01
                                             ? \Carbon\Carbon::now()->subYear(1)->format('Y')
                                             : \Carbon\Carbon::now()->format('Y')}}
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
                                        <a href="{{route('nomina.index',['id_empleado'=>$dataRolEmpleado['id_empleado'],'store'=>2])}}" target="_blank" class="btn btn-success"
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
                                <td class="text-center" style="font-weight: bold; background: #00ca6d;color: white;font-size: 1pt;">Total nómina: {{"$".number_format($total,2,".","")}}</td>
                            </tr>
                        </table>
                    @else
                        <div class="alert alert-danger col-md-12" role="alert">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            <span class="sr-only">Error:</span>
                            No se encontraton registros
                        </div>
                    @endif
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>

@endsection

@section('custom_page_js')
    @include('layouts.views.empleados.script')
@endsection
