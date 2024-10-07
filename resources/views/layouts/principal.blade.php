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


    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
        .error{
            color:red!important;
        }
        .cke_chrome{
            border:none!important;
        }
        label.error{
            position: absolute;
            color: red!important;
            width: 100%;
            left: 0;
            bottom: 0;
            top: 34px;
        }
        .daterangepicker{
            height: 373px;
        }
        .ranges{
            position: absolute;
            bottom: 0;
        }
        .form-group {
            margin-bottom: 20px!important;
        }
        .noclick{
            pointer-events: none;
            opacity: 0.80;
        }
        .loader {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url({{asset('config_empresa/loading.gif')}}) 50% 50% no-repeat rgb(249,249,249);
            opacity: .8;
        }
        div.datepicker{
            z-index: 9999!important;
        }
    </style>

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition skin-blue sidebar-mini" >
<div class="loader"></div>

@include('layouts.partials.nav')

@include('layouts.partials.aside')

<div class="content-wrapper" style="min-height: 605px;">
    @include('layouts.partials.breadcrumbs')
    <section class="content ">
        @yield('content')
    </section>
</div>

@include('layouts.partials.footer')

@include('layouts.partials.modal')
@include('layouts.partials.modal_message')
<script src="{{asset('bower_components/jquery/dist/jquery.min.js')}}"></script>
<script src="{{asset('bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<script src="{{asset('dist/js/adminlte.min.js')}}"></script>
<!--Custom js-->
<script src="{{asset('dist/js/helpers.js')}}"></script>

<!-- PACE -->
<script src="{{asset('bower_components/PACE/pace.min.js')}}"></script>

<!-- VALIDATE -->
<script src="{{asset('dist/js/jquery.validate.js')}}"></script>

<!-- CKEDITOR -->
<script src="{{asset('bower_components/ckeditor/ckeditor.js')}}"></script>



<!-- DATE RANGE PICKER -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment-with-locales.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/es.js"></script>


<!--<script src="{{asset('bower_components/jquery-ui/jquery-ui.min.js')}}"></script>-->
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"
        integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E="
        crossorigin="anonymous"></script>


<!-- FULLCALENDAR -->
<script src="{{asset('bower_components/fullcalendar/dist/fullcalendar.min.js')}}"></script>
<script src="{{asset('bower_components/fullcalendar/dist/locale/es.js')}}"></script>

<script src="{{asset('dist/js/bootbox.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/eonasdan-bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<script src="{{asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js')}}"></script>
<script src="{{asset('bower_components/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('bower_components/chart.js/Chart.js')}}"></script>
<script src="{{asset('plugins/lightbox/dist/js/lightbox.js')}}"></script>
@yield('custom_page_js')
<script>load(0);</script>
</body>
</html>
