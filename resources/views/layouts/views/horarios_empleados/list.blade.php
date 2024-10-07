@extends('layouts.principal')
@section('title')
    Configurar horas extras
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                {{-- @if(!$success)--}}
                @include('flash::message')
                {{--  @else--}}
                <div class="box-header">
                    <div class="col-md-6">
                        <h3 class="box-title">Configurar horas extras</h3>
                    </div>
                    <div class="col-md-2">
                      <button class="btn btn-primary" onclick="configurar_feriado()">
                          <i class="fa fa-calendar"></i> Configurar feriados
                      </button>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                <i class="fa fa-users" aria-hidden="true"></i>
                                Empleado
                            </span>
                            <select class="form-control" id="id_empleado" name="id_empleado" onchange="cargar_calendario(this.value)" >
                                <option selected disabled> Seleccione </option>
                                @foreach($dataEmpleados as $empleado)
                                    <option value="{{$empleado->party_id}}">{{$empleado->first_name}} {{$empleado->last_name}} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="" >
                        <section class="content">
                            <div class="row" style="margin-top: 60px">
                                <div class="col-md-3" style="box-shadow: 0px -1px 75px -21px grey;">
                                    <div class="box box-solid">
                                        <div class="box-header with-border">
                                            <h4 class="box-title">Horarios de trabajo</h4>
                                        </div>
                                        <div class="box-body">
                                            <!-- the events -->
                                            <div id="external-events"></div>
                                            <span id="msg_delete" class="error"></span>
                                        </div>
                                        <!-- /.box-body -->
                                    </div>
                                    <!-- /. box -->
                                    <div class="box box-solid">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Crear horario</h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                                                <!--<button type="button" id="color-chooser-btn" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown">Color <span class="caret"></span></button>-->
                                                <ul class="fc-color-picker" id="color-chooser">
                                                    <li onclick="class_div(this)" id="text-aqua" class="text-aqua"><i class="fa fa-square"></i></li>
                                                    <li onclick="class_div(this)" id="text-blue" class="text-blue"><i class="fa fa-square"></i></li>
                                                    <li onclick="class_div(this)" id="text-light-blue" class="text-light-blue" ><i class="fa fa-square"></i></li>
                                                    <li onclick="class_div(this)" id="text-teal" class="text-teal"><i class="fa fa-square"></i></li>
                                                    <li onclick="class_div(this)" id="text-yellow" class="text-yellow"><i class="fa fa-square"></i></li>
                                                    <li onclick="class_div(this)" id="text-orange" class="text-orange"><i class="fa fa-square"></i></li>
                                                    <li onclick="class_div(this)" id="text-green" class="text-green"><i class="fa fa-square"></i></li>
                                                    <li onclick="class_div(this)" id="text-lime" class="text-lime"><i class="fa fa-square"></i></li>
                                                    <li onclick="class_div(this)" id="text-red" class="text-red"><i class="fa fa-square"></i></li>
                                                    <li onclick="class_div(this)" id="text-purple" class="text-purple"><i class="fa fa-square"></i></li>
                                                    <li onclick="class_div(this)" id="text-fuchsia" class="text-fuchsia"><i class="fa fa-square"></i></li>
                                                    <li onclick="class_div(this)" id="text-navy" class="text-navy"><i class="fa fa-square"></i></li>
                                                    <input type="hidden" id="class" value="">
                                                </ul>
                                            </div>
                                            <!-- /btn-group -->
                                            <div class="">
                                                <form id="form_intervalo_hora">
                                                    <div class="col-md-6" style="padding: 0 5px 0 0">
                                                        <input id="entrada" placeholder="Desde" type="text" class="form-control">
                                                    </div>
                                                    <div class="col-md-6" style="padding: 0 0 0 5px">
                                                        <input id="salida" placeholder="Hasta" type="text" class="form-control">
                                                    </div>
                                                    <span id="msg" class="error"></span>

                                                    <div style="padding: 5px 0;">
                                                        <div class="col-md-12" style="padding: 0">
                                                            <button id="add-new-event" style="width: 100%" onclick="store_intervalo_horas()"
                                                                    type="button" class="btn btn-primary btn-flat">
                                                                <i class="fa fa-paper-plane-o" id="ico" aria-hidden="true"></i> Agregar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <!-- /btn-group -->
                                            </div>
                                            <!-- /input-group -->
                                        </div>
                                    </div>
                                </div>
                                <!-- /.col -->
                                <div class="col-md-9" style="box-shadow: 0px -1px 75px -21px grey;">
                                    <div class="box box-primary">
                                        <div class="box-body no-padding" id="calendario">
                                            <!-- THE CALENDAR -->
                                        </div>
                                        <!-- /.box-body -->
                                    </div>
                                    <!-- /. box -->
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </section>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

    </script>
@endsection
@section('custom_page_js')
    @include('layouts.views.horarios_empleados.script')
@endsection