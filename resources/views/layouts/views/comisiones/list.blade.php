@extends('layouts.principal')
@section('title')
    Comisiones
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="col-md-12">
                      <h3 class="box-title">Listado de comisiones</h3>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    @if(isset($dataComisiones) && count($dataComisiones) != 0)
                        <table class="table table-striped" id="tabla_tipo_comisiones">
                            <tr>
                                <th class="text-center">Fecha nómina</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Concepto de comisión</th>
                                <th class="text-center">Descripción</th>
                            </tr>
                            @foreach($dataComisiones as $comision)
                                <tr>
                                    <td class="text-center">
                                        {{$comision->fecha_nomina}}
                                    </td>
                                    <td class="text-center">
                                        {{"$".$comision->cantidad}}
                                    </td>
                                    <td class="text-center">
                                        {{getConceptoComision($comision->id_comisiones)->nombre}}
                                    </td>
                                    <td class="text-center">
                                        <i class="fa fa-2x fa-comments-o" data-toggle="popover"
                                           title="Comentario del administrador" data-trigger="hover" data-placement="left"
                                           data-content="{{$comision->descripcion}}"
                                           style="cursor:pointer;" aria-hidden="true"></i>
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
                        {!! !empty($dataComisiones->links()) ? $dataComisiones->appends(request()->input())->links() : '' !!}
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
    @include('layouts.views.comisiones.script')
@endsection