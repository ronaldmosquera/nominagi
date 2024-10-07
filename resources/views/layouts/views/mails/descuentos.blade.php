<!DOCTYPE html>
<html>
<head>
    <title>Descuentos</title>
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
            .alert-warning {
                color: #8a6d3b;
                background-color: #fcf8e3;
                border-color: #faebcc;
        }
    </style>
</head>
<body>
<div class="alert alert-warning" role="alert">
    <h4 class="alert-heading"> "Se te ha asignado un descuento!" </h4>
    <p>
        Se te ha asignado un descuento por la cantidad de ${{$cantidad}}  el cual será descontado a tu rol de pago en la nomina del mes de {{getMes(intval(\Carbon\Carbon::parse($fechaDescuento)->format('m')))." del ".\Carbon\Carbon::parse($fechaDescuento)->format('Y')}}.
        <br />
        El motivo del descuento se explica a continuación: {{$conceptoDescuento}}
    </p>
    <hr>
    <p class="mb-0">
        Correo enviado a {{$mailEmpleado}}
    </p>
</div>
</body>
</html>

