@extends('layouts.principal')
@section('title')
    Motivos de anulación de contratos
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Listado de motivos de terminación de contratos</h3>
                    <div class="box-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <div class="input-group-btn text-center" style="right: 5rem;">
                                <button type="button" data-toggle="tooltip" class="btn btn-default" id="add_tipo_contrato"
                                    onclick="add_motivo_anulacion_contrato()" title="Agregar motivo de terminación de contrato">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-striped" id="tabla_tipo_contrato">
                       @if(isset($dataMotivosAnulacionContrato) && count($dataMotivosAnulacionContrato) != 0)
                            <tr>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Descripcion</th>
                                <th class="text-center">Acción</th>
                            </tr>
                            @foreach($dataMotivosAnulacionContrato as $key => $motivosAnulacionContrato)
                                <tr>
                                    <td class="text-center">
                                        {{$motivosAnulacionContrato->nombre}}
                                    </td>

                                    <td class="text-center">
                                        {{$motivosAnulacionContrato->descripcion}}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-default" title="Editar" data-toggle="tooltip"
                                                onclick="add_motivo_anulacion_contrato('{{$motivosAnulacionContrato->id_motivo_anulacion}}')">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-{!! $motivosAnulacionContrato->estado == 1 ? "success" :  "warning" !!}"
                                                title="{!! $motivosAnulacionContrato->estado == 1 ? "Deshabilitar motivo de anulación?" :  "Habilitar motivo de anulación?" !!}"
                                                data-toggle="tooltip"  onclick="update_status('{{$motivosAnulacionContrato->id_motivo_anulacion}}','{{$motivosAnulacionContrato->estado}}')">
                                            <i class="fa fa-{!! $motivosAnulacionContrato->estado == 1 ? "check" :  "ban" !!}"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger" title="Eliminar motivo de anulacion" {!! $motivosAnulacionContrato->estado == 1 ? "disabled='disabled'" :  "" !!}
                                        onclick="delete_motivo_anulacion('{{$motivosAnulacionContrato->id_motivo_anulacion}}')" data-toggle="tooltip">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <div class="alert alert-danger" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                <span class="sr-only">Error:</span>
                                No existen motivos de anulación de contratos almacenados en la base de datos
                            </div>
                        @endif
                    </table>
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
    @include('layouts.views.motivo_anulaciones.script')
@endsection