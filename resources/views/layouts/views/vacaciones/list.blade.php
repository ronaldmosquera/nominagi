@extends('layouts.principal')
@section('title')
    Vacaciones
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                @if($message)
                    @include('flash::message')
                @else
                    <div class="box-header">
                        <h3 class="box-title">Vacaciones</h3>
                        <div class="box-tools">
                            <div class="input-group input-group-sm" style="width: 980px;">
                                <div class="input-group-btn" style="right: -3rem;bottom: 3.5px;">
                                    <form id="form_busqueda_vacaciones" name="form_busqueda_vacaciones" action="{{route('vacaciones.index')}}" method="GET" novalidate>
                                        <div class="col-md-1" style="padding: 0;"></div>
                                        <div class="col-md-3" style="padding: 0;">
                                            <div class="input-group">
                                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                   <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                                    Estado
                                                </span>
                                                <select class="form-control" id="estado" name="estado" >
                                                    <option value=""> Seleccione </option>
                                                    <option value="0"> Solicitadas </option>
                                                    <option value="1"> Aprobadas </option>
                                                    <option value="2"> No aprobadas </option>
                                                    <option value="3"> Cumpliadas </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3" style="padding: 0;">
                                            <div class="input-group">
                                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                    Desde
                                                </span>
                                                <input type="date" id="desde" name="desde" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3" style="padding: 0;">
                                            <div class="input-group">
                                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                    Hasta
                                                </span>
                                                <input type="date" id="hasta" name="hasta"  class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-1" style="padding: 0;">
                                            <button class="btn btn-default" onclick="buscar_vacaciones()">
                                                <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                                        id="title_btn_buscar"></em>
                                            </button>
                                        </div>
                                        <div class="col-md-1 text-left" style="padding: 0;right: 3.5rem;">
                                            <button type="button" data-toggle="tooltip" class="btn btn-default"
                                                    onclick="add_vacaciones('','{{session('dataUsuario')['id_empleado']}}')" title="Solicitar vacaciones">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding">
                        @if(isset($dataVacaciones) && count($dataVacaciones) != 0)
                            <table class="table table-striped" id="tabla_tipo_contrato">
                                <tr>
                                    <th class="text-center">Fecha salida</th>
                                    <th class="text-center">Fecha entrada</th>
                                    <th class="text-center">Cantidad Días</th>
                                    <th class="text-center">Días entre semana</th>
                                    <th class="text-center">Días fines de semana</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Opciones</th>
                                </tr>
                                @include('flash::message')
                                @foreach($dataVacaciones as $dataVacacion)
                                    <tr>
                                        <td class="text-center">
                                            {{$dataVacacion->fecha_inicio}}
                                        </td>
                                        <td class="text-center">
                                            {{$dataVacacion->fecha_fin}}
                                        </td>
                                        <td class="text-center">
                                            {{$dataVacacion->cantidad_dias}}
                                        </td>
                                        <td class="text-center">
                                            {{$dataVacacion->dias_entre_semana}}
                                        </td>
                                        <td class="text-center">
                                            {{$dataVacacion->dias_fines_semana}}
                                        </td>
                                        <td class="text-center">
                                            @php
                                                if($dataVacacion['estado'] == 0)
                                                    echo 'Solicitada';
                                                if($dataVacacion['estado'] == 1)
                                                    echo 'Aprobada';
                                                if($dataVacacion['estado'] == 2)
                                                    echo 'No aprobada';
                                                if($dataVacacion['estado'] == 3)
                                                    echo 'Cumplida';
                                            @endphp
                                        </td>
                                        <td class="text-center">
                                            @if($dataVacacion->estado == 0)
                                                <button type="button" class="btn btn-default"
                                                        data-toggle="tooltip" title="Editar vacaciones"
                                                        onclick="add_vacaciones('{{$dataVacacion->id_vacaciones}}')">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger"
                                                        data-toggle="tooltip" title="Elimnar vacaciones"
                                                        onclick="delete_vacaciones('{{$dataVacacion->id_vacaciones}}')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @endif
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
                            {!! !empty($dataVacaciones->links()) ? $dataVacaciones->appends(request()->input())->links() : '' !!}
                        </div>
                    </div>
                @endif
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
    @include('layouts.views.vacaciones.script')
@endsection