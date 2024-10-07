@extends('layouts.principal')
@section('title')
    Cargos
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Listado de cargos</h3>
                    <div class="box-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <div class="input-group-btn text-center" style="right: 5rem;">
                                <a class="btn btn-default" id="add_cargo" onclick="add_cargo()"
                                   data-toggle="tooltip"  title="Crear cargo"><i class="fa fa-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-striped" id="tabla_tipo_contrato">
                        <tr>
                            <th class="text-center">Cargo</th>
                            <th class="text-center">Descripción</th>
                            <th class="text-center">Cargo de confianza</th>
                            <th class="text-center">Salario mínimo sectorial</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        @foreach($dataCargos as $cargos)
                           <tr>
                               <td class="text-center">
                                   {{$cargos->nombre}}
                               </td>
                               <td class="text-center">
                                   {{$cargos->descripcion}}
                               </td>
                               <td class="text-center">
                                   {{$cargos->cargo_confianza ? "Si" : "No"}}
                               </td>
                               <td class="text-center">
                                   {{"$".$cargos->sueldo_minimo_sectorial}}
                               </td>
                               <td class="text-center">
                                   <button type="button" class="btn btn-default" title="Editar cargo"
                                           data-toggle="tooltip" onclick="add_cargo('{{$cargos->id_cargo}}')">
                                       <i class="fa fa-pencil"></i>
                                   </button>
                                   <button type="button" class="btn btn-danger" title="Eliminar cargo"
                                           data-toggle="tooltip" onclick="delete_cargo('{{$cargos->id_cargo}}')">
                                       <i class="fa fa-trash"></i>
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
    @include('layouts.views.cargo.script')
@endsection