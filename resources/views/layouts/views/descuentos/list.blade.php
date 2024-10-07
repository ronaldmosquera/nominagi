@extends('layouts.principal')
@section('title')
    Otros descuentos
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="col-md-9">
                        <h3 class="box-title">Listado de descuentos</h3>
                    </div>
                    <form name="form_descuentos" id="form_descuentos" action="{{route('vista.descuentos_empleados')}}" method="GET" novalidate>
                        <div class="col-md-3" style="padding: 0;">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                   <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                    Estado
                                </span>
                                <select class="form-control" id="estado" name="estado" onchange="document.getElementById('form_descuentos').submit()">
                                    <option selected disabled> Seleccione </option>
                                    <option value="0">No descontado</option>
                                    <option value="1">Descontado</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    @if(isset($dataDescuentos) && count($dataDescuentos) != 0)
                        <table class="table table-striped" id="tabla_tipo_descuentos">
                            <tr>
                                <th class="text-center">Fecha nómina</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Motivo</th>
                                <th class="text-center">Estado</th>
                            </tr>
                            @foreach($dataDescuentos as $descuento)
                                <tr>
                                    <td class="text-center">
                                        {{$descuento->fecha_descuento}}
                                    </td>
                                    <td class="text-center">
                                        {{"$".$descuento->cantidad}}
                                    </td>
                                    <td class="text-center">
                                        <i class="fa fa-2x fa-comments-o" data-toggle="popover"
                                           title="Descripcion" data-trigger="hover" data-placement="left"
                                           data-content="{{$descuento->descripcion}}"
                                           style="cursor:pointer;" aria-hidden="true"></i>
                                    </td>
                                    <td class="text-center">
                                        {{$descuento['descontado'] == 1 ? 'Descontado' : 'No descontado'}}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        <div class="alert alert-danger col-md-12" role="alert">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            <span class="sr-only">Error:</span>
                            No se encontraton registros
                        </div>
                    @endif
                    <div class="text-right" style="padding-right: 10px;">
                        {!! !empty($dataDescuentos->links()) ? $dataDescuentos->appends(request()->input())->links() : '' !!}
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    </section>
    <!-- /.content -->
    </div>
@endsection

@section('custom_page_js')
    @include('layouts.views.descuentos.script')
@endsection