@extends('layouts.principal')
@section('title')
    Productos
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                {{--@if(!$success)
                    @include('flash::message')
                @else--}}
                    <div class="box-header">
                        <div class="col-md-4">
                          <h3 class="box-title">Productos</h3>
                        </div>

                        <form id="form_busqueda_horas-extras" name="form_busqueda_horas-extras" action="{{route('productos.index')}}" method="GET" novalidate>
                                @csrf
                            <div class="col-md-3" style="padding: 0;">
                                <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                    <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                    Estado
                                </span>
                                    <select class="form-control" id="estado" name="estado" onchange="document.getElementById('form_busqueda_horas-extras').submit()">
                                        <option selected disabled> Seleccione </option>
                                        <option value="1"> Activas </option>
                                        <option value="0"> Inactivas </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" style="padding: 0;">
                                <div class="col-md-11 col-xs-10" style="padding: 0;">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" placeholder="Buscar">
                                        <div class="input-group-btn">
                                            <button class="btn btn-default" type="submit">
                                                <i class="glyphicon glyphicon-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-1" style="padding: 0;" >
                            <button type="button" data-toggle="tooltip" class="btn btn-default" id="btn_add_productos"
                                    onclick="add_productos()" title="Agregar productos">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding">
                        @if(isset($productos) && count($productos) != 0)
                            <table class="table table-striped" id="tabla_tipo_contrato">
                                <tr>
                                    <th class="text-center">Producto</th>
                                    <th class="text-center">Costo</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Opciones</th>
                                </tr>
                                @include('flash::message')
                                @foreach($productos as $producto)
                                    <tr>
                                        <td class="text-center">
                                            {{$producto->nombre}}
                                        </td>
                                        <td class="text-center">
                                            {{'$ ' . $producto->costo}}
                                        </td>
                                        <td class="text-center">
                                            {!! $producto->estado == 1 ? 'Activo'  : 'Inactivo'!!}
                                        </td>
                                        <td class="text-center">
                                            {{--<button type="button" class="btn btn-default"
                                                    data-toggle="tooltip" title="Editar hora extra"
                                                    onclick="add_horas_extras('{{$dataHoraExtra->id_horas_extras}}')">
                                                <i class="fa fa-pencil"></i>
                                            </button>--}}
                                            <button type="button" class="btn btn-{!! $producto->estado == 1 ? 'success'  : 'warning'!!}"
                                                    data-toggle="tooltip" title="{!! $producto->estado == 1 ? 'Desactivar'  : 'Activar'!!} producto"
                                                    onclick="desactivar_producto('{{$producto->id_productos}}','{{$producto->estado}}')">
                                                <i class="fa fa-{!! $producto->estado == 1 ? 'check'  : 'ban'!!}" aria-hidden="true"></i>
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
                            {!! !empty($productos->links()) ? $productos->appends(request()->input())->links() : '' !!}
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
    @include('layouts.views.productos.script')
@endsection