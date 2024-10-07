@extends('layouts.principal')
@section('title')
    Horas extras
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                @if(!$success)
                    @include('flash::message')
                @else
                    <div class="box-header">
                        <h3 class="box-title">Horas extras</h3>
                        <div class="box-tools">
                            <div class="input-group input-group-sm" style="width: 980px;">
                                <div class="input-group-btn" style="right: -3rem;bottom: 3.5px;">
                                    <form id="form_busqueda_horas-extras" name="form_busqueda_horas-extras" action="{{route('horas-extras.index')}}" method="GET" novalidate>
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
                                                    <option value="3"> Pagadas </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3" style="padding: 0;">
                                            <div class="input-group">
                                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                    Desde
                                                </span>
                                              <input type="date" id="desde" name="desde" class="form-control"  required>
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
                                            <button class="btn btn-default" onclick="buscar_horas_extras()">
                                                <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                                        id="title_btn_buscar"></em>
                                            </button>
                                        </div>
                                        <div class="col-md-1 text-left" style="padding: 0;right: 3.5rem;">
                                            <button type="button" data-toggle="tooltip" class="btn btn-default" id="add_tipo_contrato"
                                                    onclick="add_horas_extras()" title="Solicitar horas extras">
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
                        @if(isset($dataHorasExtras) && count($dataHorasExtras) != 0)
                            <table class="table table-striped" id="tabla_tipo_contrato">
                                <tr>
                                    <th class="text-center">Fecha de solicitud</th>
                                    <th class="text-center">Desde</th>
                                    <th class="text-center">Hasta</th>
                                    <th class="text-center">Cant. horas</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Comentarios</th>
                                    <th class="text-center">Opciones</th>
                                </tr>
                                @include('flash::message')
                                @foreach($dataHorasExtras as $dataHoraExtra)
                                    <tr>
                                        <td class="text-center">
                                            {{$dataHoraExtra->fecha_solicitud}}
                                        </td>
                                        <td class="text-center">
                                            {{$dataHoraExtra->desde}}
                                        </td>
                                        <td class="text-center">
                                            {{$dataHoraExtra->hasta}}
                                        </td>
                                        <td class="text-center">
                                            {{$dataHoraExtra->cantidad_horas}}
                                        </td>
                                        <td class="text-center">
                                            @if($dataHoraExtra->estado == 0)
                                                {{'Solicitadas'}}
                                            @elseif($dataHoraExtra->estado == 1)
                                                {{'Aprobadas'}}
                                            @elseif($dataHoraExtra->estado == 2)
                                                {{'No aprobadas'}}
                                            @elseif($dataHoraExtra->estado == 3)
                                                {{'Pagadas'}}
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <i class="fa fa-2x fa-comments-o" data-toggle="popover"
                                               title="Comentario del administrador" data-trigger="hover" data-placement="left"
                                                data-content="{{empty($dataHoraExtra['comentarios_respuesta']) ? 'Sin comentarios': $dataHoraExtra['comentarios_respuesta']}}"
                                               style="cursor:pointer;" aria-hidden="true"></i>
                                        </td>
                                        <td class="text-center">
                                            @if($dataHoraExtra->estado == 0)
                                            <button type="button" class="btn btn-default"
                                                    data-toggle="tooltip" title="Editar hora extra"
                                                    onclick="add_horas_extras('{{$dataHoraExtra->id_horas_extras}}')">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger"
                                                    data-toggle="tooltip" title="Elimnar hora extra"
                                                    onclick="delete_horas_extras('{{$dataHoraExtra->id_horas_extras}}')">
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
                           {!! !empty($dataHorasExtras->links()) ? $dataHorasExtras->appends(request()->input())->links() : '' !!}
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
    @include('layouts.views.horas_extras.script')
@endsection