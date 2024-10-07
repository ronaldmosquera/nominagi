@extends('layouts.principal')
@section('title')
    Contrataciones
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="col-md-3">
                        <h3 class="box-title">Listado de contrataciones</h3>
                    </div>
                    <div class="{{$cantPrestamos ? 'col-md-3' : 'col-md-4'}}">
                        <form id="form_filtro_tipo_contrato" action="{{route('contrataciones.index')}}" method="get">
                            <div class="input-group" style="width: 300px;float: left;margin-right: 10px;">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> Tipo de contrato </span>
                                <select id="tipo_contrato" name="tipo_contrato" onchange="document.getElementById('form_filtro_tipo_contrato').submit()" class="form-control">
                                    <option selected disabled> Seleccione </option>
                                    <option value="1"> Confidencialidad</option>
                                    <option value="2"> Contratación</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="{{$cantPrestamos ? 'col-md-3' : 'col-md-4'}}">
                        <form id="form_filtro_estado" action="{{route('contrataciones.index')}}" method="get">
                            <div class="input-group" style="width: 300px;float: left;margin-right: 10px;">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> Estados </span>
                                <select id="estado" name="estado" onchange="document.getElementById('form_filtro_estado').submit()" class="form-control">
                                    <option selected disabled> Seleccione </option>
                                    <option value="0"> En preparación</option>
                                    <option value="1"> Activos</option>
                                    <option value="2"> Anulados</option>
                                    <option value="3"> Terminados</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-1">
                        <a href="{{route('contrataciones.create')}}" data-toggle="tooltip"
                            class="btn btn-default" id="add_contrato" title="Crear contrato">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    @if($cantPrestamos)
                        <div class="col-md-2">
                            <button class="btn btn-success" onclick="form_chash_prestamos()">
                                <i class="fa fa-list-alt"></i> CASH PRESTAMOS
                            </button>
                        </div>
                    @endif
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    @if(isset($arrDataContrataciones) && count($arrDataContrataciones) != 0)
                        <table class="table table-striped" id="tabla_tipo_contrato">
                            <tr>
                                <th class="text-center">Empleado</th>
                                <th class="text-center">Tipo de contrato</th>
                                @if($arrDataContrataciones[0]['tipoContratacion'] == 2)
                                <th class="text-center">Fecha de inicio</th>
                                @endif
                                @if(isset($arrDataContrataciones[0]['fecha_finalizacion']) && $arrDataContrataciones[0]['fecha_finalizacion'])
                                <th class="text-center">Fecha de terminación</th>
                                @endif
                                <th class="text-center">Estado</th>
                                <th class="text-center">Opciones</th>
                            </tr>
                            @include('flash::message')
                            @foreach($arrDataContrataciones as $key => $dataContrataciones)
                                <tr>
                                    <td class="text-center">
                                        {{ucfirst($dataContrataciones['nombre'])}}
                                    </td>
                                    <td class="text-center">
                                        {{ucfirst($dataContrataciones['tipoContrato'])}}
                                        <i class="fa fa-question-circle" aria-hidden="true"
                                           data-toggle="tooltip" title="{{$dataContrataciones['descripcionContrato']}}">
                                        </i>
                                    </td>
                                    @if($dataContrataciones['tipoContratacion'] == 2)
                                    <td class="text-center">
                                        {{ucfirst($dataContrataciones['expedicionContrato'])}}
                                    </td>
                                    @endif
                                    @if(isset($dataContrataciones['fecha_finalizacion']) && $dataContrataciones['fecha_finalizacion'])
                                   <td class="text-center">
                                       {{ucfirst($dataContrataciones['fecha_finalizacion'])}}
                                   </td>
                                    @endif
                                   <td class="text-center">
                                        @if($dataContrataciones['estado'] == 0) {{"En preparación"}} @endif
                                        @if($dataContrataciones['estado'] == 1) {{"Activo"}} @endif
                                        @if($dataContrataciones['estado'] == 2) {{"Anulado"}} @endif
                                        @if($dataContrataciones['estado'] == 3) {{"Terminado"}} @endif
                                   </td>
                                   <td class="text-center">

                                        @if($dataContrataciones['estado'] != 0 && $dataContrataciones['estado'] != 2)
                                            <a target="_blank" href="{{asset('contratos/'.$dataContrataciones['contrato'])}}" type="button"
                                               class="btn btn-danger" data-toggle="tooltip" title="Ver contrato" >
                                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                        @if($dataContrataciones['tipoContratacion'] == 2 && ($dataContrataciones['estado'] != 0 && $dataContrataciones['estado'] != 2) )
                                            <button target="_blank" type="button" class="btn btn-danger" data-toggle="tooltip"
                                                    onclick="add_contrato_firmado('{{$dataContrataciones['idContrataciones']}}')"
                                                    title="Archivos relacionados" >

                                                <i class="fa fa-file-image-o" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                        @if($dataContrataciones['estado'] == 0)
                                                <a href="{{route('contrataciones.create',['idContratacion'=>$dataContrataciones['idContrataciones']])}}"
                                                   type="button" class="btn btn-default" data-toggle="tooltip" title="Editar Contrato"
                                                        onclick="editar_contrato('{{$dataContrataciones['idContrataciones']}}')">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger" data-toggle="tooltip" title="Anular Contrato"
                                                        onclick="anular_contrato('{{$dataContrataciones['idContrataciones']}}')">
                                                    <i class="fa fa-ban"></i>
                                                </button>
                                        @endif
                                        @if($dataContrataciones['estado'] == 1)
                                                <button type="button" class="btn btn-info" data-toggle="tooltip" title="Contrataciones y Addendum"
                                                        onclick="form_addendum_contratatacion('{{$dataContrataciones['idContrataciones']}}')">
                                                    <i class="fa fa-files-o" aria-hidden="true"></i>
                                                </button>
                                                @if($dataContrataciones['tipoContratacion'] == 2)
                                                    <button type="button" class="btn btn-success" data-toggle="tooltip" title="Bonos y prestamos"
                                                            onclick="form_bonos_fijos('{{$dataContrataciones['idContrataciones']}}')">
                                                        <i class="fa fa-money" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-warning" data-toggle="tooltip" title="Terminar Contrato"
                                                            onclick="terminar_contrato('{{$dataContrataciones['idContrataciones']}}')">
                                                            <i class="fa fa-ban"></i>
                                                    </button>
                                                @endif
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
                        {!! !empty($arrDataContrataciones->links()) ? $arrDataContrataciones->appends(request()->input())->links() : '' !!}
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
