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
                    <form method="get" action="{{route('vista.roles-pago')}}" id="form_reporte_rol">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                    <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                    Tipo
                                </span>
                                <select name="tipo" id="tipo" class="form-control">
                                    <option selected disabled="">Seleccione</option>
                                    <option value="1">Nómina</option>
                                    <option value="2">Liquidación</option>
                                    <option value="3">Alcance de nómina</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                                    <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                                    Estado
                                </span>
                                <select name="estado" id="estado" class="form-control">
                                    <option selected disabled="">Seleccione</option>
                                    <option value="1">Firmados</option>
                                    <option value="0">No firmados</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <button class="btn btn-default" id="buscar" onclick="document.getElementById('form_reporte_rol').submit()"
                                    data-toggle="tooltip" title="Buscar"><i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                    <div class="col-md-2 text-right">
                        <button style="cursor:pointer;" aria-hidden="true" onclick="form_carga_roles()" class="btn btn-success">
                            <i class="fa fa-file-image-o" aria-hidden="true"></i>
                            Roles firmados
                        </button>
                    </div>
                    </div>
                    <div class="" style="margin-bottom: 50px; padding: 0px 30px;">
                        <section class="content">
                            <div class="row" style="margin-top: 40px">
                                @if(isset($dataRoles) && count($dataRoles) != 0)
                                    @php $x = 1 @endphp
                                    @foreach($dataRoles as $key => $dataRol)
                                        @php
                                            $nominaPagada = nominaPagada($key);
                                            $mesD4to = intval(\Carbon\Carbon::parse($key)->format('m')) == 7 && pagarDecimo($key,'DECIMO_4TO');
                                            $mesD3ero = intval(\Carbon\Carbon::parse($key)->format('m')) == 11 && pagarDecimo($key,'DECIMO_3ER');
                                        @endphp
                                        <div class="box box-primary {!! $x == 1 ? "" :  "collapsed-box" !!}">
                                            <div class="box-header with-border">
                                                <div class="box-title">{{$tipo == 1 ? "Nómina" :  ($tipo==2 ? "Liquidaciones" : "Alcances" )}}
                                                    {{getMes(intval(\Carbon\Carbon::parse($key)->format('m')))}} del {{\Carbon\Carbon::parse($key)->format('Y')}}
                                                    @if($tipo == 1 && !$nominaPagada && $key > '2021-06-30')
                                                        <button onclick="form_chash('{{$key}}','{{$tipo}}')" class="btn btn-sm btn-success">
                                                            <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                            Cash managment nómina
                                                        </button>
                                                    @endif
                                                    @if($tipo == 1 && $mesD4to)
                                                        <button onclick="form_decimos('DECIMO_4TO')" class="btn btn-sm btn-info" style="margin-left: 5px">
                                                            <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                            Cash managment decimo 4to sueldo
                                                        </button>
                                                    @endif
                                                    @if($tipo == 1 && $mesD3ero)
                                                        <button onclick="form_decimos('DECIMO_3ER')" class="btn btn-sm btn-info" style="margin-left: 5px">
                                                            <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                            Cash managment decimo 3er suledo
                                                        </button>
                                                    @endif
                                                    @if($tipo==3 && $cantAlcances)
                                                        <button onclick="form_alcances_nomina()" class="btn btn-sm btn-info" style="margin-left: 5px">
                                                            <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                            Cash managment alcances de nónmina
                                                        </button>
                                                    @endif
                                                    @if($tipo==2 && $cantLiquidaciones)
                                                        <button onclick="form_liquidacion('{{$key}}')" class="btn btn-sm btn-info" style="margin-left: 5px">
                                                            <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                            Cash managment liquidaciones
                                                        </button>
                                                    @endif
                                                </div>
                                                <div class="box-tools pull-right">
                                                    <button type="button" class="btn btn-box-tool tool-plus" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                </div>
                                            </div>
                                            <div class="box-body table-responsive">
                                                <table class="table table-striped" id="tabla_tipo_contrato">
                                                    <tr>
                                                        @php
                                                            $tdPagar = false;
                                                            foreach(array_column($dataRol,'pagado') as $p){
                                                                if(!$p){
                                                                    $tdPagar=true;
                                                                    break;
                                                                }
                                                            }
                                                        @endphp
                                                        <th class="text-center">Pagar</th>
                                                        <th class="text-center">Empleado</th>
                                                        <th class="text-center">Identifiación</th>
                                                        <th class="text-center">Cuenta</th>
                                                        <th class="text-center">Fecha nómina</th>
                                                        <th class="text-center">Tipo contrato</th>
                                                        <th class="text-center">Monto</th>
                                                        <th class="text-center">Opciones</th>
                                                    </tr>
                                                    @php
                                                        $total = 0;
                                                        usort($dataRol, function($a, $b){ return strcmp($b['id_contrataciones'],$a['id_contrataciones']); });
                                                    @endphp
                                                    @foreach($dataRol as $key2 => $dR)
                                                        <tr>
                                                            @if($tipo == 1 || $tipo == 2)
                                                                <td class="text-center">
                                                                    @if(($tipo == 1 || $tipo == 2) && !$dR['pagado'] && $key > '2021-06-30')
                                                                        <input id="{{$dR['íd_empleado']}}" class="check_pago_nomina" name="{{$dR['íd_empleado']}}"
                                                                            type="checkbox" {{$tipo == 2 ? '' : 'checked'}}  >
                                                                        <label for="{{$dR['íd_empleado']}}">Por pagar</label>
                                                                    @else
                                                                        Pagado
                                                                    @endif
                                                                </td>
                                                            @endif
                                                            <td class="text-center">
                                                                {{$dR['nombre_empleado']}}
                                                            </td>
                                                            <td class="text-center">
                                                                {{$dR['identificacion']}}
                                                            </td>
                                                            <td class="text-center">
                                                                {{$dR['cuenta']}}
                                                            </td>
                                                            <td class="text-center">
                                                                {{getMes(intval(\Carbon\Carbon::parse($dR['fecha_nomina'])->format('m'))).  " del ". \Carbon\Carbon::parse($dR['fecha_nomina'])->format('Y')}}
                                                            </td>
                                                            <td class="text-center">
                                                                {{$dR['tipo_contrato']}}
                                                            </td>
                                                            <td class="text-center">
                                                                ${{$dR['monto']}}
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="{{asset(explode(".",$dR["nombre_imagen"])[1] == "pdf" ? "/roles_pago/".$dR["nombre_imagen"] : "/imagenes_roles/".$dR["nombre_imagen"])}}"
                                                                    {{explode(".",$dR["nombre_imagen"])[1] == "pdf" ? "target='_blank'" : "data-lightbox='image-1' data-lightbox='roadtrip' " }}  class="btn btn-success" data-toggle="tooltip" title="Ver rol">
                                                                    <i class="fa {{explode(".",$dR["nombre_imagen"])[1] == "pdf" ? "fa-file-pdf-o " : "fa-picture-o"}}" aria-hidden="true"></i>
                                                                </a>
                                                                @if($tipo == 1)
                                                                    <button type='button' class='btn btn-primary' data-toggle="tooltip" title="Realizar alcance de nómina"
                                                                            onclick="alcance_nomina('{{$dR['id_nomina']}}','{{$dR['nombre_empleado']}}','{{$dR['relacion_dependencia']}}')">
                                                                        <i class="fa fa-credit-card" ></i>
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @php
                                                           $total += $dR['monto']
                                                        @endphp

                                                    @endforeach
                                                    <tr>
                                                        <td colspan="6" class='text-right'><b>Total:</b></td>
                                                        <td  class='text-center'><b>${{$total}}</b></td>
                                                    </tr>
                                                </table>
                                                {{--@if($extension == "jpg" || $extension == "JPG" || $extension == "png" || $extension == "PNG")
                                                    <div class="col-md-2">
                                                        <button type="button" class="close">
                                                            <span aria-hidden="true" title="Eliminar" onclick="eliminar_imagen_rol('{{$dR["id_imagen_rol"]}}','{{$dR["nombre_imagen"]}}')" data-toggle="tooltip">&times;</span>
                                                        </button>
                                                        <a href="{{asset("/imagenes_roles/".$dR["nombre_imagen"])}}" data-lightbox="{{$key}}" data-lightbox="roadtrip">
                                                            <img src="{{asset("/imagenes_roles/".$dR["nombre_imagen"])}}" class="img-thumbnail"></a>
                                                        <div class="text-center">{{strtoupper($dR["nombre_empleado"])}}</div>
                                                    </div>
                                                @else
                                                    <div class="col-md-3 text-center" style="margin-bottom: 10px">
                                                        <a class="btn btn-danger" href="{{asset("/roles_pago/".$dR["nombre_imagen"])}}" target="_blank" >
                                                            <i class="fa fa-5x fa-file-pdf-o" aria-hidden="true"></i>
                                                        </a>
                                                        <div class="text-center">{{strtoupper($dR["nombre_empleado"])}}</div>
                                                    </div>
                                                @endif--}}
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
