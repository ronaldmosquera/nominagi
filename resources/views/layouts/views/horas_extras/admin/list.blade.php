@extends('layouts.principal')
@section('title')
Administrar horas extras
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <div class="col-md-1">
                  <h3 class="box-title">Horas extras</h3>
                </div>
                <form id="form_busqueda_horas-extras" name="form_busqueda_horas-extras"
                      action="{{route('vista.list_horas_extras_admin')}}" method="GET" novalidate>
                    <div class="col-md-3" style="padding: 0">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                Empleado
                            </span>
                            <select class="form-control" id="id_empleado" name="id_empleado" >
                                <option value=""> Seleccione </option>
                                @foreach($dataEmpleados as  $dataEmpleado)
                                    @php $person = getPerson($dataEmpleado) @endphp
                                    <option value="{{$person->party_id}}">
                                        {{$person->first_name}} {{$person->last_name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3" style="padding: 0">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
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
                    <div class="col-md-2" style="padding: 0">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                Desde
                            </span>
                            <input type="date" id="desde" name="desde" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-2" style="padding: 0">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                Hasta
                            </span>
                            <input type="date" id="hasta" name="hasta" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-1" style="padding: 0;text-align: right">
                        <button class="btn btn-default" onclick="buscar_horas_extras()">
                            <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                    id="title_btn_buscar"></em>
                        </button>
                    </div>
                </form>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
                <div class="text-right" style="margin-right: 10px">
                    <input type="checkbox" id="todos"
                           onclick="$('input:checkbox[id=todos]').is(':checked') ? $('input:checkbox[name=checkHoraExtra]').attr('checked',true) : $('input:checkbox[name=checkHoraExtra]').attr('checked',false)">
                        ¿Marcar todos?
                </div>
                @if(isset($dataHorasExtras) && count($dataHorasExtras))
                    <table class="table table-striped" id="tabla_tipo_contrato">
                        <tr>
                            <th class="text-center">Empleado</th>
                            <th class="text-center">Fecha de solicitud</th>
                            <th class="text-center">Desde</th>
                            <th class="text-center">Hasta</th>
                            <th class="text-center">Cant. horas</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Comentarios</th>
                            <th class="text-center">Seleccionar</th>
                        </tr>
                        @include('flash::message')
                        @foreach($dataHorasExtras as $dataHoraExtra)
                            <tr>
                                <td class="text-center">
                                    {{$dataHoraExtra['nombre']}}
                                </td>
                            <td class="text-center">
                                {{$dataHoraExtra['fecha_solicitud']}}
                            </td>
                            <td class="text-center">
                                {{$dataHoraExtra['desde']}}
                            </td>
                            <td class="text-center">
                                {{$dataHoraExtra['hasta']}}
                            </td>
                            <td class="text-center">
                                {{$dataHoraExtra['cantidad_horas']}}
                            </td>
                            <td class="text-center">
                                @if($dataHoraExtra['estado'] == 0)
                                    {{'Solicitadas'}}
                                @elseif($dataHoraExtra['estado'] == 1)
                                    {{'Aprobadas'}}
                                @elseif($dataHoraExtra['estado'] == 2)
                                    {{'No aprobadas'}}
                                @elseif($dataHoraExtra['estado'] == 3)
                                    {{'Pagadas'}}
                                @endif
                            </td>
                            <td class="text-center">
                                <i class="fa fa-2x fa-comments-o" data-toggle="popover" @if($dataHoraExtra['estado'] == 0 ) ondblclick="responder({{$dataHoraExtra['id_horas_extras']}})" @endif
                                    title="Comentarios (Haz doble click para responder)" data-trigger="hover" data-placement="left"
                                    data-content="{{empty($dataHoraExtra['comentarios']) ? 'Sin comentarios': $dataHoraExtra['comentarios']}}"
                                    style="cursor:pointer;" aria-hidden="true"></i>
                            </td>
                                @if($dataHoraExtra['estado'] == 0 && (Carbon\Carbon::parse($dataHoraExtra['fecha_solicitud'])->diffInDays(now()) <= $tiempoAprovHe))
                                    <td class="text-center">
                                        <input type="checkbox" value="{{$dataHoraExtra['id_horas_extras']}}" name="checkHoraExtra">
                                    </td>
                                @elseif($dataHoraExtra['estado'] != 0)
                                    <td></td>
                                @elseif(Carbon\Carbon::parse($dataHoraExtra['fecha_solicitud'])->diffInDays(now()) > $tiempoAprovHe)
                                    <td class="text-center error"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>Vencida (Nunca aprobada)</td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                    @if($dataHoraExtra['estado'] == 0)
                        <div class="text-right" style="padding: 10px;">
                            <button type="button" class="btn btn-success" onclick="save_success_horas_extras()">
                                <i id="ico" class="fa fa-check" aria-hidden="true"></i>
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
                {!! !empty($dataHorasExtras->links()) ? $dataHorasExtras->appends(request()->input())->links() : '' !!}
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
@include('layouts.views.horas_extras.script')
@endsection
