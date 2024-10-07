@extends('layouts.principal')
@section('title')
    Otros descuentos
@endsection

@section('content')
    <section>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="col-md-4">
                        <h3 class="box-title">Listado de descuentos</h3>
                    </div>
                    <form name="form_descuentos" action="{{route('otros-descuentos.index')}}" method="GET" novalidate>
                        <div class="col-md-3" style="padding: 0;">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    Empleado
                                </span>
                                <select class="form-control" id="id_empleado" name="id_empleado">
                                    <option selected disabled> Seleccione </option>
                                    @foreach($dataEmpleados as $empleados)
                                        <option value="{{$empleados->party_id}}">{{$empleados->first_name}} {{$empleados->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" style="padding: 0;">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                   <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                    Estado
                                </span>
                                <select class="form-control" id="estado" name="estado">
                                    <option selected disabled> Seleccione </option>
                                    <option value="0">No descontado</option>
                                    <option value="1">Descontado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1" style="padding: 0;">
                            <button type="submit" class="btn btn-default" >
                                <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                        id="title_btn_buscar"></em>
                            </button>
                        </div>
                        <div class="col-md-1">
                            <div class="input-group-btn text-center" style="right: 5rem;">
                                <a class="btn btn-default" id="add_descuento" onclick="add_descuento()"
                                   data-toggle="tooltip"  title="Crear descuentos">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    @if(isset($dataDescuentos) && count($dataDescuentos) != 0)
                        <table class="table table-striped" id="tabla_tipo_descuentos">
                            <tr>
                                <th class="text-center">Empleado</th>
                                <th class="text-center">Fecha nómina</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Concepto</th>
                                <th class="text-center">Descripción</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Opciones</th>
                            </tr>
                            @foreach($dataDescuentos as $descuento)
                                <tr>
                                    <td class="text-center">
                                        {{$descuento->persona}}
                                    </td>
                                    <td class="text-center">
                                        {{$descuento->fecha_descuento}}
                                    </td>
                                    <td class="text-center">
                                        {{"$".$descuento->cantidad}}
                                    </td>
                                    <td class="text-center">
                                        {{$descuento->nombre}}
                                    </td>
                                    <td class="text-center">
                                        <i class="fa fa-2x fa-comments-o" data-toggle="popover"
                                           title="Descripcion" data-trigger="hover" data-placement="left"
                                           data-content="{{$descuento->descripcion}}"
                                           style="cursor:pointer;" aria-hidden="true"></i>
                                    </td>
                                    <td class="text-center">
                                        {{$descuento->descontado == 1 ? 'Descontado' : 'No descontado'}}
                                    </td>
                                    <td class="text-center">
                                        @if($descuento->descontado == 0)
                                            <button type="button" class="btn btn-default"
                                                    data-toggle="tooltip" title="Editar descuento"
                                                    onclick="add_descuento('{{$descuento->id_descuento}}')">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger"
                                                    data-toggle="tooltip" title="Elimnar descuento"
                                                    onclick="delete_descuento('{{$descuento->id_descuento}}')">
                                                <i class="fa fa-trash"></i>
                                            </button>
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
                        {!! !empty($dataDescuentos->links()) ? $dataDescuentos->appends(request()->input())->links() : '' !!}
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    </section>

@endsection

@section('custom_page_js')
    @include('layouts.views.descuentos.script')
@endsection
