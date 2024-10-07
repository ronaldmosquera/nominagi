<!DOCTYPE html>
<html>
<head>
    <title>Comisiones</title>
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
    </style>
</head>
<body>
    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading"> "Muy bien!" </h4>
        <p>
            Se te ha asignado una comsión por la cantidad de ${{$cantidad}} por concepto de {{$conceptoComision}} la cual será abonada a tu rol de pago del mes de {{getMes(intval(\Carbon\Carbon::parse($fechaComision)->format('m')))." del ".\Carbon\Carbon::parse($fechaComision)->format('Y')}}.
            <br />
            El motivo de la comsión se explica a continuación: {{$descripcion}}
        </p>
        <hr>
        <p class="mb-0">
           Correo enviado a {{$mailEmpleado}}
        </p>
    </div>
</body>
</html>

