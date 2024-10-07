@extends('layouts.principal')
@section('title')
    Anticipos
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="col-md-8">
                      <h3 class="box-title">Listado de anticipos</h3>
                    </div>
                    <div class="col-md-3">
                        <form id="form_busqueda_anticipos" name="form_busqueda_anticipos" action="{{route('anticipos.index')}}" method="GET" novalidate>
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                    <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                    Estado
                                </span>
                                <select class="form-control" id="estado" name="estado"
                                        onchange="document.getElementById('form_busqueda_anticipos').submit()">
                                    <option selected disabled> Seleccione </option>
                                    <option value="0"> Solicitados </option>
                                    <option value="1"> Aprobados </option>
                                    <option value="2"> No aprobados </option>
                                    <option value="3"> Descontadas </option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-1">
                        <a class="btn btn-default" id="add_cargo" onclick="add_anticipo()"
                           data-toggle="tooltip" title="Solicitar anticipo"><i class="fa fa-plus"></i>
                        </a>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    @if(isset($dataAnticipo) && count($dataAnticipo) != 0)
                        <table class="table table-striped" id="tabla_tipo_contrato">
                            <tr>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Fecha de entrega</th>
                                <th class="text-center">Fecha de descuento</th>
                                <th class="text-center">Comentarios</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                            @foreach($dataAnticipo as $anticipo)
                                <tr>
                                    <td class="text-center">
                                        {{"$ ".$anticipo->cantidad}}
                                    </td>
                                    <td class="text-center">
                                        {{$anticipo->fecha_entrega}}
                                    </td>
                                    <td class="text-center">
                                        {{$anticipo->fecha_descuento}}
                                    </td>
                                    <td class="text-center">
                                        @if(!empty($anticipo->comentario))
                                            <i class="fa fa-2x fa-comments-o" data-toggle="popover"
                                               title="Comentario del administrador" data-trigger="hover" data-placement="left"
                                               data-content="{{empty($anticipo->comentario) ? 'Sin comentarios': $anticipo->comentario}}"
                                               style="cursor:pointer;" aria-hidden="true"></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-default" title="Editar anticipo"
                                                data-toggle="tooltip" onclick="add_anticipo('{{$anticipo->id_anticipo}}')">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger" title="Eliminar anticipo"
                                                data-toggle="tooltip" onclick="delete_anticipo('{{$anticipo->id_anticipo}}')">
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
                        {!! !empty($dataAnticipo->links()) ? $dataAnticipo->appends(request()->input())->links() : '' !!}
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
    @include('layouts.views.anticipos.script')
@endsection