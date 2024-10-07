@extends('layouts.principal')
@section('title')
    Vacaciones
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
              {{-- @if(!$success || !$annosDiferencia)
                    @include('flash::message')
                @else--}}
                    <div class="box-header">
                        <div class="col-md-2">
                            <h3 class="box-title">Vacaciones</h3>
                        </div>
                        <form id="form_busqueda_vacaciones" name="form_busqueda_vacaciones" action="{{route('vista.list_vacaciones_admin')}}"
                              method="GET" novalidate>
                            <div class="col-md-3" style="padding: 0">
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
                            <div class="col-md-3" style="padding: 0">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                        Desde
                                    </span>
                                    <input type="date" id="desde" name="desde" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3" style="padding: 0">
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
                        </form>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding">
                        <div class="text-right" style="margin-right: 10px">
                            <input type="checkbox" id="todos" onclick="$('input:checkbox[id=todos]').is(':checked') ? $('input:checkbox[name=check_vacaciones]').attr('checked',true) : $('input:checkbox[name=check_vacaciones]').attr('checked',false)">
                            ¿Aprobar todos?
                        </div>
                    @if(isset($dataVacaciones) && count($dataVacaciones) != 0)
                            <table class="table table-striped" id="tabla_tipo_contrato">
                                <tr>
                                    <th class="text-center">Empleado</th>
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
                                            {{$dataVacacion['nombre']}}
                                        </td>

                                        <td class="text-center">
                                            {{$dataVacacion['fecha_inicio']}}
                                        </td>
                                        <td class="text-center">
                                            {{$dataVacacion['fecha_fin']}}
                                        </td>
                                        <td class="text-center">
                                            {{$dataVacacion['cant_dias']}}
                                        </td>
                                        <td class="text-center">
                                            {{$dataVacacion['dias_entre_semana']}}
                                        </td>
                                        <td class="text-center">
                                            {{$dataVacacion['dias_fin_semana']}}
                                        </td>
                                        <td class="text-center">
                                            @if($dataVacacion['estado'] == 0)
                                                   Solicitada
                                            @elseif($dataVacacion['estado'] == 1)
                                                    Aprobada
                                            @elseif($dataVacacion['estado'] == 2)
                                                    No aprobada
                                            @elseif($dataVacacion['estado'] == 3)
                                                    echo 'Cumplida';
                                            @endif
                                        </td>
                                        @if($dataVacacion['estado'] == 0)
                                            <td class="text-center">
                                                <input type="checkbox"  style="transform: scale(1.2);position: relative;top: 3px;" id="{{$dataVacacion['id_empleado']}}" value="{{$dataVacacion['id_vacaciones']}}"
                                                       data-toggle="tooltip" title="Aprobar vacaciones" name="check_vacaciones">
                                                <input type="checkbox"  style="transform: scale(1.2);position: relative;top: 3px;" value="{{$dataVacacion['id_vacaciones']}}"
                                                       data-toggle="tooltip" title="No aprobar vacaciones" name="check_no_vacaciones"
                                                       id="{{$dataVacacion['id_vacaciones']}}" onchange="form_comentario_vacaciones_no_aprobadas(this)">
                                                <button type="button" class="btn btn-default "
                                                        data-toggle="tooltip" title="Editar vacaciones"
                                                        onclick="edit_vacaciones_admin('{{$dataVacacion['id_vacaciones']}}')">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                            @if($dataVacacion['estado'] == 0)
                                <div class="text-right" style="padding: 10px;">
                                    <button type="button" id="btn_vacaciones" class="btn btn-success" onclick="save_success_vacaciones()">
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
                            {!! !empty($dataVacaciones->links()) ? $dataVacaciones->appends(request()->input())->links() : '' !!}
                        </div>
                    </div>
            {{--@endif--}}
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