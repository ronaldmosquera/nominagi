@extends('layouts.principal')
@section('title')
    Roles de pago
@endsection
@section('content')
    <div class="">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header" >
                    <div class="col-md-12">
                    <div class="col-md-3">
                        <h3 class="box-title">Roles de nómina</h3>
                    </div>
                    </div>
                    <div class="" style="margin-bottom: 50px;padding: 0px 30px;">
                        <section class="content">
                            <div class="row" style="margin-top: 40px">
                                @if(isset($dataRoles) && count($dataRoles) != 0)
                                    @php $x = 1 @endphp
                                    @foreach($dataRoles as $key => $dataRol)
                                        <div class="box box-primary {!! $x == 1 ? "" :  "collapsed-box" !!}">
                                            <div class="box-header with-border">
                                                    <h3 class="box-title">{{$tipo == 1 ? "Nómina" : "Liquidación"}}
                                                    {{getMes(intval(\Carbon\Carbon::parse($key)->format('m')))}} del {{\Carbon\Carbon::parse($key)->format('Y')}}
                                                </h3>
                                                <div class="box-tools pull-right">
                                                    <button type="button" class="btn btn-box-tool tool-plus" data-widget="collapse">
                                                        <i class="fa fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="box-body table-responsive">
                                                    <table class="table table-striped" id="tabla_tipo_contrato">
                                                        <tr>
                                                            <th class="text-center">Empleado</th>
                                                            <th class="text-center">Identifiación</th>
                                                            <th class="text-center">Fecha nómina</th>
                                                            <th class="text-center">Opciones</th>
                                                        </tr>
                                                       @foreach($dataRol as $key => $dR)
                                                            <tr>
                                                                <td class="text-center">
                                                                    {{$dR['nombre_empleado']}}
                                                                </td>
                                                                <td class="text-center">
                                                                    {{$dR['identificacion']}}
                                                                </td>
                                                                <td class="text-center">
                                                                    {{getMes(intval(\Carbon\Carbon::parse($dR['fecha_nomina'])->format('m'))).  " del ". \Carbon\Carbon::parse($dR['fecha_nomina'])->format('Y')}}
                                                                </td>
                                                                <td class="text-center">
                                                                    <a href="{{asset(explode(".",$dR["nombre_imagen"])[1] == "pdf" ? "/roles_pago/".$dR["nombre_imagen"] : "/imagenes_roles/".$dR["nombre_imagen"])}}"
                                                                    {{explode(".",$dR["nombre_imagen"])[1] == "pdf" ? "target='_blank'" : "data-lightbox='image-1' data-lightbox='roadtrip' " }}  class="btn btn-success" data-toggle="tooltip" title="Ver rol">
                                                                        <i class="fa {{explode(".",$dR["nombre_imagen"])[1] == "pdf" ? "fa-file-pdf-o " : "fa-picture-o"}}" aria-hidden="true"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </table>
                                            </div>
                                        </div>
                                        @php $x++ @endphp
                                    @endforeach
                                @else
                                    <div class="alert alert-danger col-md-12" role="alert">
                                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                        <span class="sr-only">Error:</span>
                                        No se encontraton registros
                                    </div>
                                @endif
                            </div>
                            <div class="text-center">
                                {!! !empty($dataRoles->links()) ? $dataRoles->appends(request()->input())->links() : '' !!}
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_page_js')
    @include('layouts.views.nomina.script')
@endsection
