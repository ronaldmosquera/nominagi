@extends('layouts.principal')
@section('title')
    Empleados
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Listado de empleados </h3>
                    <div class="box-tools">
                      <div class="input-group input-group-sm" style="width: 300px;">
                            <div class="input-group-btn" style="right: 4rem;bottom: 3.5px;">
                                <form id="form_filtro_estado" action="{{route('empleados.index')}}" method="get">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> Filtrar por estados </span>
                                            <select id="estado" name="estado" onchange="document.getElementById('form_filtro_estado').submit()" class="form-control">
                                                <option selected disabled> Seleccione </option>
                                                <option value="1"> Usuarios activos</option>
                                                <option value="0"> Usuarios inactivos</option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    @if(isset($dataEmpleados) && count($dataEmpleados) != 0)
                        <table class="table table-striped" id="tabla_tipo_contrato">

                            <tr>
                                <th class="text-center">Nombres</th>
                                <th class="text-center">Apellidos</th>
                                <th class="text-center">Idetificación</th>
                                <th class="text-center">Correo</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Opciones</th>
                            </tr>
                            @include('flash::message')
                               @foreach($dataEmpleados as $dataEmpleado)
                                    <tr>
                                        <td class="text-center">
                                            {{$dataEmpleado->first_name}}
                                        </td>
                                        <td class="text-center">
                                            {{$dataEmpleado->last_name}}
                                        </td>
                                        <td class="text-center">
                                            {{$dataEmpleado->id_value}}
                                        </td>
                                        <td class="text-center">
                                            {{getMailEmpleado($dataEmpleado->party_id)}}
                                        </td>
                                        <td class="text-center">
                                            {!! $dataEmpleado->status == 1 ? 'Activo'  : 'Inactivo'!!}
                                        </td>
                                        <td class="text-center">
                                             <button type="button" class="btn btn-default"
                                                     data-toggle="tooltip" title="Editar empleado"
                                                     onclick="editar_empleado('{{$dataEmpleado->party_id}}')">
                                                 <i class="fa fa-pencil"></i>
                                             </button>
                                             <button type="button" class="btn btn-{!! $dataEmpleado->status == 1 ? 'warning'  : 'success'!!}"
                                                     data-toggle="tooltip" title="{!! $dataEmpleado->status == 1 ? 'Desactivar empleado'  : 'Activar empleado'!!}"
                                                     onclick="update_status_empleado('{{$dataEmpleado->party_id}}','{{$dataEmpleado->status}}')">
                                                 <i class="fa fa-{!! $dataEmpleado->status == 1 ? 'ban'  : 'check'!!}"></i>
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
                        {!! !empty($dataEmpleados->links()) ? $dataEmpleados->links() : '' !!}
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
    @include('layouts.views.empleados.script')
@endsection