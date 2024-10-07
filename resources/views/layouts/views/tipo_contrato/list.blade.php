@extends('layouts.principal')
@section('title')
    Tipo de contratos
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Listado de tipos de contrato</h3>
                    <div class="box-tools">

                        <div class="input-group input-group-sm" style="width: 150px;">
                            <div class="input-group-btn text-center" style="right: 5rem;">
                                <button type="button" data-toggle="tooltip" class="btn btn-default" id="add_tipo_contrato" onclick="add_tipo_contrato()" title="Agregar tipo de contrato"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-striped" id="tabla_tipo_contrato">
                        @if(isset($dataTipoContratos) && count($dataTipoContratos) != 0)
                            <tr>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Tipo de contrato</th>
                                <th class="text-center">¿Relación de dependencia?</th>
                                <th class="text-center">Beneficios</th>
                                {{--<th class="text-center">¿Horas extra?</th>--}}
                                {{--<th class="text-center">¿Vacaciones?</th>--}}
                                <th class="text-center">Descripcion</th>
                                <th class="text-center">Acción</th>
                            </tr>
                            @foreach($dataTipoContratos as $key => $tipoContrato)
                                <tr>
                                    <td class="text-center">
                                        {{$tipoContrato->nombre}}
                                    </td>
                                    <td class="text-center">
                                        {{$tipoContrato->descripcion_tipo_contrato}}
                                    </td>
                                    <td class="text-center">
                                        {{$tipoContrato->relacion_dependencia == 1 ? 'Si': 'No'}}
                                    </td>
                                    <td class="text-center">
                                        <i class="fa fa-2x fa-handshake-o" data-toggle="popover"
                                           title="Beneficios del tipo de contrato" data-trigger="hover" data-placement="left" data-html="true"
                                           data-content="{{$tipoContrato->horas_extras == 1 ? 'Horas extras: Si <br />': 'Horas extras: No <br />' }}
                                           {{$tipoContrato->relacion_dependencia == 1 ? 'Vacaciones: Si <br />': 'Vacaciones: No <br />'}}
                                           {{$tipoContrato->relacion_dependencia == 1 ? '10mo 3eros: Si <br />': '10mo 3eros: No <br />'}}
                                           {{$tipoContrato->relacion_dependencia == 1 ? '10mo 4tos: Si <br />': '10mo 4tos: No <br />'}}
                                           {{$tipoContrato->relacion_dependencia == 1 ? 'Fondo de reserva: Si <br />': 'Fondo de reserva: No <br />'}}"
                                           style="cursor:pointer;" aria-hidden="true"></i>
                                    </td>
                                    <td class="text-center">
                                        {{$tipoContrato->descripcion}}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-default" title="Editar" onclick="add_tipo_contrato('{{$tipoContrato->id_tipo_contrato}}')"><i class="fa fa-pencil"></i></button>
                                        <button type="button" class="btn btn-danger" {{-- class="btn btn-{!! $tipoContrato->estado == 1 ? "success" :  "warning" !!}" title="{!! $tipoContrato->estado == 1 ? "Deshabilitar tipo de contrato?" :  "Habilitar tipo de contrato?" !!}" --}} data-toggle="tooltip"
                                                onclick="update_status('{{$tipoContrato->id_tipo_contrato}}','{{$tipoContrato->estado}}')">
                                                <i class="fa fa-trash"></i>
                                            {{-- <i class="fa fa-{!! $tipoContrato->estado == 1 ? "check" :  "ban" !!}"></i> --}}
                                        </button>
                                        {{-- <button type="button" class="btn btn-danger" title="Eliminar" {!! $tipoContrato->estado == 1 ? "disabled='disabled'" :  "" !!}  onclick="delete_tipo_contrato('{{$tipoContrato->id_tipo_contrato}}')"><i class="fa fa-trash"></i></button> --}}

                                    </td>
                                </tr>

                            @endforeach
                        @else
                            <div class="alert alert-danger" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                <span class="sr-only">Error:</span>
                                No existen tipos de contratos almacenados en la base de datos
                            </div>
                        @endif
                    </table>
                    <div class="text-right" style="padding-right: 10px;">
                        {!! !empty($dataTipoContratos->links()) ? $dataTipoContratos->links() : '' !!}
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
@include('layouts.views.tipo_contrato.script')
@endsection
