@extends('layouts.principal')
@section('title')
    Permisos
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box  box-info">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#asigna_permiso">Asignación de permisos</a></li>
                    <li><a data-toggle="tab" href="#edita_permisos">Editar permisos</a></li>
                </ul>
                <div class="tab-content">
                    <div id="asigna_permiso" class="tab-pane fade in active">
                        <div class="box-body table-responsive no-padding">
                            <div class="col-md-3">
                                <div class="box ">
                                    <div class="box-header">
                                        <h3 class="box-title">Roles</h3>
                                    </div>
                                    <table class="table table-striped">
                                        <tr>
                                            <th class="text-center">Nombre</th>
                                            <th class="text-center">Agregar</th>
                                        </tr>
                                        @foreach($roles as $y => $rol)
                                            <tr class="tr_check_individual">
                                                <th class="text-center">{{$rol->description}}</th>
                                                <th class="text-center" style="vertical-align: middle;">
                                                    <input type="checkbox" id="check_rol" class="check_rol" {{--onclick="seleccionar_check(this,'check_rol')"--}}
                                                           {{--{{(in_array($rol->role_type_id, $rolsSeccionMenu)) ? 'checked' : '' }}--}}
                                                           name="check_rol" value="{{$rol->role_type_id}}">
                                                </th>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">
                                            <i class="fa fa-bars" ></i> Menú
                                        </h3>
                                    </div>
                                    <div>
                                        <table class="table table-striped" id="tabla_tipo_contrato">
                                            <tr>
                                                <th class="text-center">Nombre</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                            @if($secciones->count() > 0)
                                                @foreach($secciones as $seccion)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{$seccion->nombre}}
                                                        </td>
                                                        <td class="text-center" style="vertical-align: middle">
                                                            <button class="btn btn-default" onclick="ver_seccion('{{$seccion->id_seccion_menu}}','sub_seccion_menu')">
                                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="2">
                                                        <div class="alert alert-info text-center">
                                                  No se encontraron registros
                                              </div>
                                                    </td>

                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" id="sub_seccion_menu"></div>
                        </div>
                    </div>
                    <div id="edita_permisos" class="tab-pane fade">
                        <div class="box-body table-responsive no-padding">
                            <div class="col-md-3">
                                <div class="box ">
                                    <div class="box-header">
                                        <h3 class="box-title">Roles</h3>
                                    </div>
                                    <table class="table table-striped">
                                        <tr>
                                            <th class="text-center">Nombre</th>
                                            <th class="text-center">Agregar</th>
                                        </tr>
                                        @foreach($roles as $y => $rol)
                                            <tr class="tr_check_individual">
                                                <th class="text-center">{{$rol->description}}</th>
                                                <th class="text-center" style="vertical-align: middle;">
                                                    <input type="checkbox" id="check_rol_editar" class="check_rol_editar" onclick="seleccionar_check(this,'check_rol_editar')"
                                                           {{--{{(in_array($rol->role_type_id, $rolsSeccionMenu)) ? 'checked' : '' }}--}}
                                                           name="check_rol" value="{{$rol->role_type_id}}">
                                                </th>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">
                                            <i class="fa fa-bars"></i> Menú
                                        </h3>
                                    </div>
                                    <div>
                                        <table class="table table-striped" id="tabla_tipo_contrato">
                                            <tr>
                                                <th class="text-center">Nombre</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                            @if($secciones->count() > 0)
                                                @foreach($secciones as $seccion)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{$seccion->nombre}}
                                                        </td>
                                                        <td class="text-center" style="vertical-align: middle">
                                                            <button class="btn btn-default" onclick="ver_seccion('{{$seccion->id_seccion_menu}}','sub_seccion_menu_edit')">
                                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="2">
                                                        <div class="alert alert-info text-center">
                                                  No se encontraron registros
                                              </div>
                                                    </td>

                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" id="sub_seccion_menu_edit"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_page_js')
    @include('layouts.views.permisos.script')
@endsection