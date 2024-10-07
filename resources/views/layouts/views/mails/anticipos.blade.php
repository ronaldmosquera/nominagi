<!DOCTYPE html>
<html>
<head>
    <title>Anticipo</title>
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
<div class="alert alert-{!! $estado == 1 ? "success" : "warning"!!}" role="alert">
    <h4 class="alert-heading">{!! $estado == 1 ? "Muy bien!" : "Oooooooh! :("!!}</h4>
    <p>
        @if($estado == 1)
            Tu anticipo por la cantidad ${{$dataAnticipo->cantidad}} ha sido aprobado!, será entrgado en fecha {{$dataAnticipo->fecha_entrega}}, y será descontado de tu rol de pagos a partir del mes de {{getMes(intval(\Carbon\Carbon::parse($dataAnticipo->fecha_descuento)->format('m')))." del ".\Carbon\Carbon::parse($dataAnticipo->fecha_descuento)->format('Y')}}.
        @else
            Lo sentimos el anticipo solicitado no ha sido aprobado, el motivo será descrito en el siguiente comentario del administrador: <br />{{$message1}}.
        @endif
    </p>
    <hr>
    <p class="mb-0">
        @if($estado == 1)
            Para mayor información por favor dirigirse a la administración de la sede.
        @endif
        <br />  Correo enviado a  {{$mailEmpleado}}
    </p>
</div>
</body>
</html>

