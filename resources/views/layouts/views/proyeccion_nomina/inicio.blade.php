@extends('layouts.principal')
@section('title')
    Proyección nómina
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li id="li_proyeccion" class="active">
                        <a href="#a_proyeccion"  data-toggle="tab">
                            <i class="fa fa-user-circle-o"></i>
                            Empleados y Fecha
                            <i class="fa fa-calendar"></i>
                        </a>
                    </li>
                    <li id="li_proyeccion_empleado" class="noclick">
                        <a href="#a_proyeccion" data-toggle="tab">
                            <i class="fa fa-bar-chart" aria-hidden="true"></i> Proyección
                        </a>
                    </li>
                    <div class="pull-right col-md-6 text-right">
                        <button onclick="descargar_programacion()" class="btn btn-primary" title="Descargar formato" >
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Descargar formato
                        </button>
                        <button type="button" class="btn btn-success" style="margin-right: 10px;" title="Subir programación" onclick="form_proyeccion()">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Subir programación
                        </button>
                    </div>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="a_proyeccion">
                        <div class="">
                            <div class="col-md-8">
                                <div class="">
                                    <div class="">
                                        <div class="box box-primary direct-chat direct-chat-primary">
                                            <div class="box-header with-border">
                                                <h3 class="box-title">
                                                    Empleados
                                                </h3>
                                            </div>
                                            <div class="box-body">
                                                <table class="table table-striped" id="tabla_empleados">
                                                    <tr>
                                                        <th style="width: 20%" class="text-center">Seleccionar</th>
                                                        <th >Empleado</th>
                                                    </tr>
                                                    @foreach($dataEmpleados as $key => $empleado)
                                                        <tr>
                                                            <td class="text-center">
                                                                <input type="checkbox" id="empleado_{{$key+1}}"  checked class="empleado" name="empleado" value="{{$empleado->party_id}}">
                                                            </td>

                                                            <td>
                                                                {{strtoupper($empleado->first_name. " ". $empleado->last_name)}}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="">
                                    <div class="">
                                        <div class="box box-primary direct-chat direct-chat-primary">
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Fecha</h3>
                                            </div>
                                            <div class="box-body">
                                                <div class="direct-chat-messages" style="height: 180px;">
                                                    <form id="form_fechas">
                                                        <div class="input-group" >
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                                <i class="fa fa-calendar"></i> Año
                                                            </span>
                                                            <select id="anno" name="anno" class="form-control" required>
                                                                @if(count($aanos)<1)
                                                                    <option select disabled>No se ha cargado programación</option>
                                                                @endif
                                                                @foreach($aanos as $anno)
                                                                    <option value="{{$anno->anno}}">{{$anno->anno}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="input-group" style="margin-top: 20px">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                                <i class="fa fa-calendar"></i> Comienzo
                                                            </span>
                                                            <select id="fecha_inicio_calculo" name="fecha_inicio_calculo" class="form-control" required>
                                                                @for($i=1;$i<=12;$i++)
                                                                    <option value="{{$i}}">{{getMes($i)}}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <div class="input-group" style="margin-top: 20px">
                                                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                                <i class="fa fa-calendar"></i> Fin
                                                            </span>
                                                            <select id="fecha_fin_calculo" name="fecha_fin_calculo" class="form-control" required>
                                                                @for($i=1;$i<=12;$i++)
                                                                    <option value="{{$i}}">{{getMes($i)}}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <button type="button" class="btn bg-olive btn-flat margin" onclick="proyectar()">
                                                Siguiente <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer" style="text-align: right;">

                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="a_proyeccion_programacion"></div>
                </div>
                <!-- /.tab-content -->
            </div>
            <!-- nav-tabs-custom -->
        </div>
        <!-- /.col -->
    </div>
@endsection

@section('custom_page_js')
    @include('layouts.views.proyeccion_nomina.script')
@endsection