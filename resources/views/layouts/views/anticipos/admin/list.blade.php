@extends('layouts.principal')
@section('title')
    Anticipos
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="col-md-2">
                        <h3 class="box-title">Listado de anticipos</h3>
                    </div>
                        <form id="form_busqueda_anticipos" name="form_busqueda_empleado"
                              action="{{route('vista.admin_anticipos')}}" method="GET" novalidate>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                        <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                        Fecha
                                    </span>
                                    <input type="date" class="form-control" id="fecha" name="fecha">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                        <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                        Empelado
                                    </span>
                                    <select class="form-control" id="id_empleado" name="id_empleado" >
                                        <option selected disabled> Seleccione </option>
                                        @foreach($dataEmpleados as $dataEmpleado)
                                            <option value="{{$dataEmpleado->party_id}}">
                                                {{$dataEmpleado->first_name}} {{$dataEmpleado->last_name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <form id="form_busqueda_anticipos" name="form_busqueda_anticipos" action="{{route('anticipos.index')}}" method="GET" novalidate>
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                            <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                            Estado
                                        </span>
                                        <select class="form-control" id="estado" name="estado">
                                            <option selected disabled> Seleccione </option>
                                            <option value="0"> Solicitados </option>
                                            <option value="1"> Aprobados </option>
                                            <option value="4"> Pagados </option>
                                            <option value="2"> No aprobados </option>
                                            <option value="3"> Descontadas </option>
                                        </select>
                                    </div>

                                    </div>
                                <div class="col-md-1">
                                    <button class="btn btn-default" type="submit">
                                        <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                                id="title_btn_buscar"></em>
                                    </button>
                                </div>
                                </form>
                        @if($cantAntcipos)
                            <div class='col-md-12 text-right' style='margin-top:10px'>
                                <button type="button" class="btn btn-success" title="Descargar cash m"
                                    data-toggle="tooltip" onclick="from_cash_management_anticipo()">
                                    CASH ANTICIPOS
                                </button>
                            </div>
                        @endif
                    </div>

                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    @if(isset($dataAnticipo) && count($dataAnticipo) != 0)
                        <table class="table table-striped" id="tabla_tipo_contrato">
                            <tr>
                                <th class="text-center">Empleado</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Fecha de entrega</th>
                                <th class="text-center">Fecha de descuento</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                            @foreach($dataAnticipo as $anticipo)
                                <tr>
                                    <td class="text-center">
                                        {{$anticipo['nombre']}}
                                    </td>
                                    <td class="text-center">
                                        {{"$ ".$anticipo['cantidad']}}
                                    </td>
                                    <td class="text-center">
                                        {{$anticipo['fecha_entrega']}}
                                    </td>
                                    <td class="text-center">
                                        {{$anticipo['fecha_descuento']}}
                                    </td>
                                    <td class="text-center">
                                        @if($anticipo['estado'] == 0)  Solicitado @endif
                                        @if($anticipo['estado'] == 1)  Aprobado    @endif
                                        @if($anticipo['estado'] == 4)  Pagado @endif
                                        @if($anticipo['estado'] == 2)  No aprobado @endif
                                        @if($anticipo['estado'] == 3)  Descontado @endif
                                    </td>
                                    @if($anticipo['estado'] == 0)
                                    <td class="text-center">
                                        <input type="checkbox"  style="transform: scale(1.2);position: relative;top: 3px;" value="{{$anticipo['id_anticipo']}}"
                                            data-toggle="tooltip" title="Aprobar anticipo" name="check_anticipo">
                                        <input type="checkbox"  style="transform: scale(1.2);position: relative;top: 3px;" value="{{$anticipo['id_anticipo']}}"
                                            data-toggle="tooltip" title="No aprobar anticipo" name="check_no_anticipo"
                                            id="{{$anticipo['id_anticipo']}}" onchange="form_comentario_anticipo_no_aprobado(this)">
                                        <button type="button" class="btn btn-default" title="Editar anticipo"
                                                data-toggle="tooltip" onclick="edit_anticipo_admin('{{$anticipo['id_anticipo']}}')">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </table>
                        @if($anticipo['estado'] == 0)
                        <div class="text-right" style="padding: 10px;">
                            <button type="button" id="btn_anticipo" class="btn btn-success" onclick="save_success_anticipos()">
                                <i id="ico"  class="fa fa-floppy-o" aria-hidden="true"></i>
                                Aprobar
                            </button>
                        </div>
                        @endif
                    @else
                        <div class="alert alert-danger col-md-12" role="alert">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            <span class="sr-only">Error:</span>
                            No se encontraton registros
                        </div>
                    @endif
                    <div class="text-right" style="padding-right: 10px;">
                        {!! !empty($dataAnticipo->links()) ? $dataAnticipo->appends(request()->input())->links() : '' !!}
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            </div>
            <!-- /.box -->
        </div>
    </div>
    </section>
    <!-- /.content -->
    </div>
@endsection

@section('custom_page_js')
    @include('layouts.views.anticipos.script')
@endsection
