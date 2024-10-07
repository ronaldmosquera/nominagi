@extends('layouts.principal')
@section('title')
    Comisiones
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="col-md-4">
                        <h3 class="box-title">Listado de comisiones</h3>
                    </div>
                    <form name="form_comisiones" action="{{route('comisiones.index')}}" method="GET" novalidate>
                        <div class="col-md-3" style="padding: 0;">
                            <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                            Fecha
                                        </span>
                                <input type="date" id="fecha_nomina" name="fecha_nomina" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3" style="padding: 0;">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                    <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                    Empleado
                                </span>
                                <select class="form-control" id="id_empleado" name="id_empleado">
                                    <option selected disabled> Seleccione </option>
                                    @foreach($dataEmpleados as $empleados)
                                        <option value="{{$empleados->party_id}}">{{$empleados->first_name}} {{$empleados->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1" style="padding: 0;">
                            <button type="submit" class="btn btn-default" >
                                <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                        id="title_btn_buscar"></em>
                            </button>
                        </div>
                        <div class="col-md-1">
                            <div class="input-group-btn text-center" style="right: 5rem;">
                                <a class="btn btn-default" id="add_cargo" onclick="add_comision()"
                                   data-toggle="tooltip"  title="Crear comisiones">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    @if(isset($dataComisiones) && count($dataComisiones) != 0)
                        <table class="table table-striped" id="tabla_tipo_comisiones">
                            <tr>
                                <th class="text-center">Empleado</th>
                                <th class="text-center">Fecha nómina</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Concepto</th>
                                <th class="text-center">Comentario</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                            @foreach($dataComisiones as $comision)
                                <tr>
                                    <td class="text-center">
                                        {{$comision['nombre']}}
                                    </td>
                                    <td class="text-center">
                                        {{$comision['fecha_nomina']}}
                                    </td>
                                    <td class="text-center">
                                        {{$comision['cantidad']}}
                                    </td>
                                    <td class="text-center">
                                        {{$comision['concepto']}}
                                    </td>
                                    <td class="text-center">
                                        <i class="fa fa-2x fa-comments-o" data-toggle="popover"
                                           title="Comentario" data-trigger="hover" data-placement="left"
                                           data-content="{{$comision['descripcion']}}"
                                           style="cursor:pointer;" aria-hidden="true"></i>
                                    </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-default" title="Editar comsión"
                                                    data-toggle="tooltip" onclick="add_comision('{{$comision['id_comision']}}')">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger" title="Eliminar comisión"
                                                    data-toggle="tooltip" onclick="delete_comision('{{$comision['id_comision']}}')">
                                                <i class="fa fa-trash"></i>
                                            </button>
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
                        {!! !empty($dataComisiones->links()) ? $dataComisiones->appends(request()->input())->links() : '' !!}
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
    @include('layouts.views.comisiones.script')
@endsection
