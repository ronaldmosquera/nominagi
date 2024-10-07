@extends('layouts.principal')
@section('title')
    Documentos
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                @if(isset($success) && !$success)
                    @include('flash::message')
                @else
                    <div class="box-header">
                        <div class="col-md-7">
                            <h3 class="box-title">Documentos</h3>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <div class="input-group-btn" >
                                    <form id="form_busqueda_documentos" name="form_busqueda_documentos-extras" action="{{route('documentos.index')}}"
                                          method="GET" onchange="document.getElementById('form_busqueda_documentos').submit()" novalidate>
                                        @csrf
                                        <div>
                                            <div class="input-group" style="width: 100%">
                                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                   <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                                    Estado
                                                </span>
                                                    <select class="form-control" id="estado" name="estado" >
                                                    <option selected disabled> Seleccione </option>
                                                    <option value="1"> Activos </option>
                                                    <option value="0"> Inactivos </option>
                                                </select>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1 text-left">
                            <a href="{{route('documentos.create')}}" type="button" data-toggle="tooltip"
                               class="btn btn-default" id="add_tipo_contrato" title="Crear documento">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding">
                        @if(isset($dataDocumentos) && count($dataDocumentos) != 0)
                            <table class="table table-striped" id="tabla_tipo_contrato">
                                <tr>
                                    <th class="text-center">Nombre del documento</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Opciones</th>
                                </tr>
                                @include('flash::message')
                                @foreach($dataDocumentos as $documentos)
                                    <tr>
                                        <td class="text-center">
                                            {{$documentos->nombre}}
                                        </td>
                                        <td class="text-center">
                                            {{$documentos->estado == 1 ? 'Activo' : 'Inactivo'}}
                                        </td>
                                        <td class="text-center">
                                            @if($documentos->estado == 1 )
                                            <a href="{{route('documentos.edit',$documentos->id_documentos)}}" type="button" class="btn btn-default"
                                                    data-toggle="tooltip" title="Editar documento">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            @endif
                                            <button class="btn btn-{{$documentos->estado == 1 ? 'success' : 'warning'}}"
                                                    onclick="update_estado_documento('{{$documentos->estado}}','{{$documentos->id_documentos}}')"
                                                    title="{{$documentos->estado == 1 ? 'Documento activado' : 'Documento inactivo'}}">
                                                <i class="fa fa-{{$documentos->estado == 1 ? 'check' : 'ban'}}" aria-hidden="true"></i>
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
                            {!! !empty($dataDocumentos->links()) ? $dataDocumentos->appends(request()->input())->links() : '' !!}
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
    @include('layouts.views.documentos.script')
@endsection