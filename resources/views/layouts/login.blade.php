<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Innofarm | @yield('title')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{asset('bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('bower_components/font-awesome/css/font-awesome.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{asset('bower_components/Ionicons/css/ionicons.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('dist/css/AdminLTE.min.css')}}">

    <!-- FULLCALENDAR -->
    <link rel="stylesheet" href="{{asset('bower_components/fullcalendar/dist/fullcalendar.min.css')}}">


    <link rel="stylesheet" href="{{asset('dist/css/skins/skin-blue.min.css')}}">

    <link rel="stylesheet" href="{{asset('plugins/pace/pace.min.css')}}">
    <link rel="stylesheet" href="{{asset('bower_components/bootstrap-daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/lightbox/dist/css/lightbox.min.css')}}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/eonasdan-bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css">
</head>

<style>
    .login-box, .register-box {
        width: 360px;
        margin: 7% auto;
    }
    .login-box-body, .register-box-body {
        background: #fff;
        padding: 20px;
        border-top: 0;
        color: #666;
        box-shadow: 2px 2px 5px #999;
    }
</style>
    <body style="background: #f3f3f3;">
        <div class='login-box'>
            <div class='text-center' style="margin:30px 0">
                <img alt="Login" src="/config_empresa/grupo-inno.jpg" style="width: 250px;">
            </div>
            <div class="login-box-body">
                <p class="login-box-msg">INGRESO AL SISTEMA DE NÓMINA</p>
                <p>@include('flash::message')</p>
                <form action="{{url('access_user')}}" method="post">
                {{@csrf_field()}}
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" value="{{old('usuario')}}" name="usuario" required placeholder="Usuario">
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    @if ($errors->has('contrasena'))
                        <div class="text-danger">{{ $errors->first('usuario') }}</div>
                    @endif
                </div>
                <div class="form-group has-feedback">
                    <input class="form-control" type="password" name="contrasena" required  placeholder="Contraseña">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    @if ($errors->has('contrasena'))
                        <div class="text-danger">{{ $errors->first('contrasena') }}</div>
                    @endif
                </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">
                            <i class="fa fa-sign-in" aria-hidden="true"></i> Ingresar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <script src="{{asset('bower_components/jquery/dist/jquery.min.js')}}"></script>
        <script src="{{asset('bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>

    </body>
</html>
