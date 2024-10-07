@extends('layouts.principal')
@section('title')
    Roles de pago
@endsection
@section('content')
    <div class="">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header" >
                    <div class="" style="padding: 0px 30px;">
                        <section class="content">
                            <div class="row">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Roles de pago</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    @if(isset($dataRoles) && count($dataRoles) != 0)
                                    <div class="box-body">
                                        @foreach($dataRoles as $dR)
                                            <div class="col-md-2">
                                                <a href="{{asset("/imagenes_roles/".$dR['nombre_imagen'])}}" data-lightbox="all" data-lightbox="roadtrip">
                                                    <img src="{{asset("/imagenes_roles/".$dR['nombre_imagen'])}}" class="img-thumbnail">
                                                </a>
                                                <div class="text-center">{{getMes(intval(\Carbon\Carbon::parse($dR['fecha_nomina'])->format('m')))}} del {{\Carbon\Carbon::parse($dR['fecha_nomina'])->format('Y')}}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
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
