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
                    <div class="col-md-5">
                        <h3 class="box-title">Consumos Empleados</h3>
                    </div>
                    <form id="form_busqueda_consumos" name="form_busqueda_consumos" action="{{route('vista.admin_consumos')}}" method="GET" novalidate>
                        <div class="col-md-4" style="padding: 0;">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                    <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                    Empleado
                                </span>
                                <select class="form-control" id="id_empleado" name="id_empleado" onchange="document.getElementById('form_busqueda_consumos').submit()">
                                    <option selected disabled> Seleccione </option>
                                    @foreach($dataEmpleados as $dataEmpleado)
                                        <option value="{{$dataEmpleado->party_id}}">
                                            {{$dataEmpleado->first_name}} {{$dataEmpleado->last_name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" style="padding: 0;">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                    <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                    Estado
                                </span>
                                <select class="form-control" id="estado" name="estado" onchange="document.getElementById('form_busqueda_consumos').submit()">
                                    <option selected disabled> Seleccione </option>
                                    <option value="INVOICE_PAID"> Pagadas </option>
                                    <option value="INVOICE_READY"> Por pagar </option>
                                </select>
                            </div>
                        </div>
                        {{--<div class="col-md-1" style="padding: 0;">
                            <div class="col-md-1 col-xs-2" style="padding: 0;">
                                <button class="btn btn-default">
                                    <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                            id="title_btn_buscar"></em>
                                </button>
                            </div>
                        </div>--}}
                    </form>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    @if(isset($dataConsumo) && count($dataConsumo) > 0)
                        <table class="table table-striped" id="tabla_tipo_contrato">
                            <tr>
                                <th class="text-center">Empleado</th>
                                <th class="text-center">Número de factura</th>
                                <th class="text-center">Fecha factura</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Pagado</th>
                                <th class="text-center">Por pagar</th>
                                @if($estado !== "INVOICE_PAID")
                                <th class="text-center">Opciones</th>
                                @endif
                            </tr>
                            @include('flash::message')
                            @foreach($dataConsumo as $consumo)
                                <tr>
                                    <td class="text-center">
                                        {{$consumo->first_name}} {{$consumo->last_name}}
                                    </td>
                                    <td class="text-center">
                                        {{$consumo->invoice_number}}
                                    </td>
                                    <td class="text-center">
                                        {{\Carbon\Carbon::parse($consumo->invoice_date)->format('d-m-Y')}}
                                    </td>
                                    <td class="text-center">
                                        {{'$ ' . number_format($consumo->total,2,".","")}}
                                    </td>
                                    <td class="text-center">
                                        {{'$ ' .number_format($consumo->pagado,2,".","")}}
                                    </td>
                                    <td class="text-center">
                                        {{'$ ' .number_format($consumo->saldo,2,".","")}}
                                    </td>
                                    @if($estado !== "INVOICE_PAID")
                                    <td class="text-center">
                                        <button type="button" class="btn btn-success"
                                            data-toggle="tooltip" title="Generar debito"
                                            onclick="add_consumo('{{$consumo->invoice_id}}','{{$consumo->party_id}}')">
                                            <i class="fa fa-usd" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </table>
                        {{--@if($consumo['estado'] == 0)
                            <div class="text-right" style="padding: 10px;">
                                <button type="button" id="btn_consumo" class="btn btn-success" onclick="save_success_consumo()">
                                    <i id="ico"  class="fa fa-floppy-o" aria-hidden="true"></i>
                                    Aprobar
                                </button>
                            </div>
                        @endif--}}
                    @else
                        <div class="alert alert-danger col-md-12" role="alert">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            <span class="sr-only">Error:</span>
                            No se encontraton registros
                        </div>
                    @endif
                    <div class="text-right" style="padding-right: 10px;">
                        {!! !empty($dataConsumo->links()) ? $dataConsumo->appends(request()->input())->links() : '' !!}
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