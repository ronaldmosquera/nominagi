@extends('layouts.principal')
@section('title')
    Contrataciones
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <div class="col-md-3">
                        <h3 class="box-title">Listado de contrataciones</h3>
                    </div>
                    <div class="col-md-4">
                    <form id="form_filtro_tipo_contrato" action="{{route('vista.contrataciones')}}" method="get">

                        <div class="input-group" >
                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> Tipo de contrato </span>
                            <select id="tipo_contrato" name="tipo_contrato" onchange="document.getElementById('form_filtro_tipo_contrato').submit()" class="form-control">
                                <option selected disabled> Seleccione </option>
                                <option value="1"> Confidencialidad</option>
                                <option value="2"> Contratación</option>
                            </select>
                        </div>
                    </form>
                    </div>
                    <div class="col-md-2">
                    <form id="form_filtro_estado" action="{{route('vista.contrataciones')}}" method="get">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> Estados </span>
                            <select id="estado" name="estado" onchange="document.getElementById('form_filtro_estado').submit()" class="form-control">
                                <option selected disabled> Seleccione </option>
                                <option value="1"> Activos</option>
                                <option value="2"> Anulados</option>
                                <option value="3"> Terminados</option>
                            </select>
                        </div>
                    </form>
                    </div>
                    <div class="col-md-3">
                        <button onclick="documentos_firmados({{$dataContrataciones}})" type="button"
                                class="btn btn-info">
                            <i class="fa fa-file-image-o" aria-hidden="true"></i> Documentos firmados
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    @if(isset($dataContrataciones) && count($dataContrataciones) != 0)

                        <table class="table table-striped" id="tabla_tipo_contrato">
                            <tr>
                                <th class="text-center">Tipo de contrato</th>
                                <th class="text-center">Fecha de inicio</th>
                                @if(isset($dataContrataciones[0]->fecha_finalizacion))
                                    <th class="text-center">Fecha de terminación</th>
                                @endif
                                <th class="text-center">Benficios</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Opciones</th>
                            </tr>
                            @foreach($dataContrataciones as $key => $dataContratacion)

                                <tr>
                                    <td class="text-center">
                                        {{ucfirst($dataContratacion->nombre)}}
                                    </td>
                                    @if(isset($dataContratacion->fecha_finalizacion))
                                        <td class="text-center">
                                            {{ucfirst($dataContratacion->fecha_finalizacion)}}
                                        </td>
                                    @endif
                                    <td class="text-center">
                                        {{ucfirst($dataContratacion->fecha_expedicion_contrato)}}
                                    </td>
                                    <td class="text-center">
                                        <i class="fa fa-2x fa-handshake-o" data-toggle="popover"
                                           title="Beneficios del tipo de contrato" data-trigger="hover" data-placement="left" data-html="true"
                                           data-content="{{$dataContratacion->horas_extras == 1 ? 'Horas extras: Si <br />': 'Horas extras: No <br />' }}
                                           {{$dataContratacion->relacion_dependencia == 1 ? 'Vacaciones: Si <br />': 'Vacaciones: No <br />'}}
                                           {{$dataContratacion->relacion_dependencia == 1 ? '10mo 3eros: Si <br />': '10mo 3eros: No <br />'}}
                                           {{$dataContratacion->relacion_dependencia == 1 ? '10mo 4tos: Si <br />': '10mo 4tos: No <br />'}}
                                           {{$dataContratacion->relacion_dependencia == 1 ? 'Fondo de reserva: Si <br />': 'Fondo de reserva: No <br />'}}"
                                           style="cursor:pointer;" aria-hidden="true"></i>
                                    </td>
                                    <td class="text-center">
                                        @if($dataContratacion->estado == 0) {{"En preparación"}} @endif
                                        @if($dataContratacion->estado == 1) {{"Activo"}} @endif
                                        @if($dataContratacion->estado == 2) {{"Anulado"}} @endif
                                        @if($dataContratacion->estado == 3) {{"Terminado"}} @endif
                                    </td>
                                    <td class="text-center">
                                        <a target="_blank" href="{{asset('contratos/'.$dataContratacion->nombre_archivo_contrato)}}" type="button"
                                           class="btn btn-danger" data-toggle="tooltip" title="Ver contrato" >
                                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                        </a>

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
                        {!! !empty($dataContrataciones->links()) ? $dataContrataciones->appends(request()->input())->links() : '' !!}
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
    @include('layouts.views.contrataciones.script')
@endsection