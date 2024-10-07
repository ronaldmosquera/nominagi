@extends('layouts.principal')
@section('title')
    IVA
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="col-md-9">
                        <h3 class="box-title">Listado de iva</h3>
                    </div>
                    <div class="col-md-3 text-center">
                        <button data-toggle="tooltip" onclick="add_iva()"
                           class="btn btn-default" id="add_iva" title="Crear iva">
                            <i class="fa fa-plus"></i>
                        </button>
                        </div>
                    </div>
                <div class="box-body table-responsive no-padding">
                    @if(isset($dataIva) && count($dataIva) != 0)
                        <table class="table table-striped" id="tabla_tipo_descuentos">
                            <tr>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Valor</th>
                                <th class="text-center">Opciones</th>
                            </tr>
                            @foreach($dataIva as $iva)
                                <tr>
                                    <td class="text-center">
                                        {{$iva->nombre}}
                                    </td>
                                    <td class="text-center">
                                        {{$iva->valor."%"}}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-default" onclick="add_iva('{{$iva->id_iva}}')">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="delete_iva('{{$iva->id_iva}}')">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
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
                </div>
                </div>
                <!-- /.box-header -->

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
    @include('layouts.views.iva.script')
@endsection