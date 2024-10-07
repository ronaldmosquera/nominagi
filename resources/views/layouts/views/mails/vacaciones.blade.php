<!DOCTYPE html>
<html>
<head>
    <title>Vacaciones</title>
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        .alert-warning {
            color: #8a6d3b;
            background-color: #fcf8e3;
            border-color: #faebcc;
        }
        .alert-warning hr {
            border-top-color: #f7e1b5;
        }
    </style>
</head>
<body>
<div class="alert alert-{!! $status == 1 ? "success" : "warning"!!}" role="alert">
    <h4 class="alert-heading">{!! $status == 1 ? "Muy bien!" : "Oooooooh! :("!!}</h4>
    <p>
        @if($status == 1)
           Tu periodo vacional ha sido aprobado!, puedes disfrutar de él desde la fecha {{$desde}}, hasta la fecha {{$hasta}} debiendote reincorporar a tus actividades laborales el día: {{$reincorporacion}}
        @else
            Lo sentimos tus vacaciones no han sido aprobadas, el motivo será descrito en el siguiente comentario del administrador: <br />{{$message1}}.
        @endif
    </p>
    <hr>
    <p class="mb-0">
        @if($status == 1)
            Disfruta de tus vacaciones...!
        @else
            Para mayor información por favor dirigirse a la administración de la sede.
        @endif
            <br />  Correo enviado a  {{$mailEmpleado}}
    </p>
</div>
</body>
</html>

