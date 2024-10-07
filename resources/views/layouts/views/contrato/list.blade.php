@extends('layouts.principal')
@section('title')
    Contratos
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Listado de contrato</h3>

                    <div class="box-tools">

                        <div class="input-group input-group-sm" style="width: 150px;">
                            <div class="input-group-btn text-center" style="right: 5rem;">
                                <a href="{{route('contrato.create')}}" class="btn btn-default" id="add_contratacion" title="Crear contratatación"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-striped" id="tabla_tipo_contrato">

                            <tr>
                                <th class="text-center">Contrato</th>
                                <th class="text-center">Tipo de contrato</th>
                                {{--<th class="text-center">Descripción</th>--}}
                                <th class="text-center">Opciones</th>
                            </tr>
                        @include('flash::message')
                        @foreach($dataContratos as $contrato)
                            <tr>
                                <td class="text-center">
                                    {{$contrato->nombre}}
                                </td>
                                <td class="text-center">
                                    {{$contrato->descripcion_tipo_contrato}}
                                </td>
                                {{-- <td class="text-center">
                                     {{$contrato->descripcion_contrato}}
                                </td>--}}
                                <td class="text-center">
                                    <a href="{{route('contrato.edit',$contrato->id_contrato)}}" data-toggle="tooltip" type="button"
                                       class="btn btn-default" title="Editar contrato" ><i class="fa fa-pencil"></i></a>
                                    <button type="button" class="btn btn-danger" {{-- class="btn btn-{!! $contrato->cestado == 1 ? 'success' : 'warning' !!}" --}} data-toggle="tooltip"
                                            title="{!! $contrato->cestado == 1 ? 'Deshabilitar contrato?' : 'Habilitar contrato?' !!}"
                                            onclick="update_estado_contrato('{{$contrato->id_contrato}}','{{$contrato->cestado}}')">
                                            <i class="fa fa-trash"></i>
                                        {{-- <i class="fa fa-{!! $contrato->cestado == 1 ? 'check' : 'ban' !!}"></i> --}}
                                    </button>
                                    {{-- <button type="button" class="btn btn-danger"
                                            data-toggle="tooltip" {!! $contrato->cestado == 1 ? "disabled='disabled'" : "" !!} title="Eliminar contrato"
                                            onclick="delete_contrato('{{$contrato->id_contrato}}')">
                                        <i class="fa fa-trash"></i>
                                    </button> --}}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="text-right" style="padding-right: 10px;">

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
    @include('layouts.views.contrato.script')
@endsection
