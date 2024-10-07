@extends('layouts.principal')
@section('title')
    Tipo comisiones
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Listado de tipos de comisiones</h3>
                    <div class="box-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <div class="input-group-btn text-center" style="right: 5rem;">
                                <a class="btn btn-default" id="add_tipo_comision" onclick="add_tipo_comision()"
                                   data-toggle="tooltip"  title="Crear tipo comision"><i class="fa fa-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-striped" id="tabla_tipo_contrato">
                        <tr>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Monto estandar</th>
                            <th class="text-center">¿Calcula 10mo 3er Sueldo?</th>
                            <th class="text-center">Descripción</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        @foreach($dataTipoComision as $tipoComision)
                            <tr>
                                <td class="text-center">
                                    {{$tipoComision->nombre}}
                                </td>
                                <td class="text-center">
                                    {{"$".$tipoComision->estandar}}
                                </td>
                                <td class="text-center">
                                    {{$tipoComision->calculo_decimo_tercero == 1 ? "Sí" : "No"}}
                                </td>
                                <td class="text-center">
                                    <i class="fa fa-2x fa-comment-o" data-toggle="popover"
                                       title="Descripción" data-trigger="hover" data-placement="top"
                                       data-content="{{$tipoComision->descripcion}}"
                                       style="cursor:pointer;" aria-hidden="true"></i>

                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-default" title="Editar tipo comision"
                                            data-toggle="tooltip" onclick="add_tipo_comision('{{$tipoComision->id_tipo_comision}}')">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-{{$tipoComision->estado == 0 ? 'warning' : 'success' }}" title="{{$tipoComision->estado == 0 ? 'Habilitar' : 'Deshabilitar' }} tipo comision"
                                            data-toggle="tooltip" onclick="update_comision('{{$tipoComision->id_tipo_comision}}','{{$tipoComision->estado}}')">
                                        <i class="fa fa-{{$tipoComision->estado == 0 ? 'ban' : 'check' }}"></i>
                                    </button>
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
    @include('layouts.views.tipo_comisiones.script')
@endsection