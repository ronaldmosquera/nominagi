@extends('layouts.principal')
@section('title')
    Perfil de usuario
@endsection

@section('content')
<div class="col-md-12">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#activity" data-toggle="tab" aria-expanded="true">Perfil del usaurio</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="activity">
                <!-- Post -->
                <div class="post">
                  <div class="user-block">
                      <i class="fa fa-3x fa-user-circle img-circle img-bordered-sm" style="float: left;" aria-hidden="true"></i>
                        <span class="username" style="margin-left:60px;margin-top: 13px;">
                          <a href="#">{{ ucwords(session('dataUsuario')['primer_nombre']) ." ".  ucwords(session('dataUsuario')['apellido']) }}.</a>
                        </span>
                        <span class="description"></span>
                    </div>
                    <!-- /.user-block -->
                    <p>
                        <!-- BAR CHART -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <div class="col-md-9">
                              <h3 class="box-title">
                                  <i class="fa fa-line-chart" aria-hidden="true"></i>
                                  Estadísticas nómina <span id="anno"></span>
                              </h3>
                            </div>
                            <div class="box-tools pull-right col-md-3">
                                <div style="padding: 0;">
                                    <div class="input-group">
                                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                                    Año
                                                </span>
                                        <select class="form-control" id="fecha" name="fecha" onchange="estadisticas_nomina()">
                                            <option value="{{\Carbon\Carbon::now()->format('Y')}}"> Seleccione </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="chart">
                                <canvas id="barChart" style="height:230px"></canvas>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>

                    </p>
                </div>
                <!-- /.post -->
            </div>
            <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
    </div>
    <!-- /.nav-tabs-custom -->
</div>
@endsection

@section('custom_page_js')
    @include('layouts.views.usuario.script')
@endsection
