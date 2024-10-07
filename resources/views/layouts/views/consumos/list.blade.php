@extends('layouts.principal')
@section('title')
    Consumos
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                {{--@if(!$success)
                    @include('flash::message')
                @else--}}
                <div class="box-header">
                    <div class="col-md-6">
                        <h3 class="box-title">Consumos</h3>
                    </div>
                    <form id="form_busqueda_consumos" name="form_busqueda_consumos" action="{{route('consumos.index')}}" method="GET" novalidate>

                        <div class="col-md-3" style="padding: 0;">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                    <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                    Estado
                                </span>
                                <select class="form-control" id="estado" name="estado" onchange="document.getElementById('form_busqueda_consumos').submit()">
                                    <option selected disabled> Seleccione </option>
                                    <option value="0"> No descontado </option>
                                    <option value="1"> Descontado </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" style="padding: 0;">
                            <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                        Fecha
                                    </span>
                                <input type="date" id="fecha" name="fecha" class="form-control" onchange="document.getElementById('form_busqueda_consumos').submit()" required>
                            </div>
                        </div>
                    </form>
                    {{--<div class="col-md-1" style="padding: 0;">
                        <div class="col-md-1 col-xs-2" style="padding: 0;">
                            <button class="btn btn-default" onclick="buscar_horas_extras()">
                                <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                        id="title_btn_buscar"></em>
                            </button>
                        </div>
                    </div>
                </form>--}}
                {{--<div class="col-md-1 col-xs-2" style="padding: 0;" >
                    <button type="button" data-toggle="tooltip" class="btn btn-default" id="btn_solicitar_consumo"
                            onclick="add_consumo()" title="Solicitar consumo">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>--}}
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    @if(isset($data_consumo) && count($data_consumo) != 0)
                        <table class="table table-striped" id="tabla_tipo_contrato">
                            <tr>
                                <th class="text-center">Factura</th>
                                <th class="text-center">Fecha descuento</th>
                                <th class="text-center">Monto descuento</th>
                                <th class="text-center">Estado</th>
                            </tr>
                            @include('flash::message')
                            @foreach($data_consumo as $consumo)
                                <tr>
                                    <td class="text-center">
                                        {{$consumo->invoice_id}}
                                    </td>
                                    <td class="text-center">
                                        {{$consumo->fecha_descuento}}
                                    </td>
                                    <td class="text-center">
                                        {{"$".number_format($consumo->monto_descuento,2,".","")}}
                                    </td>
                                    <td class="text-center">
                                        {{$consumo->estado == false ? "No descontado" : "Descontado"}}
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
                        {!! !empty($data_consumo->links()) ? $data_consumo->appends(request()->input())->links() : '' !!}
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
    @include('layouts.views.consumos.script')
@endsection