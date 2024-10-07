@extends('layouts.principal')
@section('title')
    Lista de pagos
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            @php $x = 1 @endphp
            @foreach($dataGeneral as $key => $dataRol)
                <div class="box box-primary {!! $x == 1 ? "" :  "collapsed-box" !!}">
                    <div class="box-header with-border">
                        <h3 class="box-title">Nómina
                            {{getMes(intval(\Carbon\Carbon::parse($key)->format('m')))}} del {{\Carbon\Carbon::parse($key)->format('Y')}}
                        </h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool tool-plus" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <table class="table table-striped" id="tabla_tipo_contrato">
                            <tr>
                                <th class="text-center">Fecha nómina</th>
                                <th class="text-center">Empleado</th>
                                <th class="text-center">Identifiación</th>
                                <th class="text-center">Cargo</th>
                                <th class="text-center">Total ingresos</th>
                                <th class="text-center">Rol empleado</th>
                            </tr>
                            @php $total = 0; @endphp
                            @foreach($dataRol as $key => $dR)
                                <tr>
                                    <td class="text-center">
                                        {{$dR['fecha_nomina']}}
                                    </td>
                                    <td class="text-center">
                                        {{$dR['nombre']}}
                                    </td>
                                    <td class="text-center">
                                        {{$dR['identificacion']}}
                                    </td>
                                    <td class="text-center">
                                        {{$dR['cargo']}}
                                    </td>
                                    <td class="text-center">
                                        {{"$".number_format($dR['total'],2,".","")}}
                                    </td>
                                    {{--}<td class="text-center">
                                        {{getMes(intval(\Carbon\Carbon::parse($dR['fecha_nomina'])->format('m'))).  " del ". \Carbon\Carbon::parse($dR['fecha_nomina'])->format('Y')}}
                                    </td>--}}
                                    <td class="text-center">
                                        <a target="_blank" href="{{asset("/roles_pago/".$dR["nombre_imagen"])}}"
                                            class="btn btn-success" data-toggle="tooltip" title="Ver rol">
                                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                                @php $total += number_format($dR['total'],2,".",""); @endphp
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-center" style="font-weight: bold; background: #00ca6d;color: white;font-size: 12pt;">Total nómina: {{"$".number_format($total,2,".","")}}</td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </div>
                @php $x++ @endphp
            @endforeach
        </div>
        <div class="text-center">
            {!! !empty($dataGeneral->links()) ? $dataGeneral->appends(request()->input())->links() : '' !!}
        </div>
    </div>
    <!-- /.content -->
@endsection
