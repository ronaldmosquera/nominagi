@extends('layouts.principal')
@section('title')
    Acceso denegado
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-danger">
                <div class="text-center"><img src="{{asset('denegado.png')}}"></div>
                <div class='alert alert-danger text-center'>
                    <h5>No tienes permisos para realizar esta acción o ver esta área</h5>
                </div>
            </div>
        </div>
    </div>
@endsection