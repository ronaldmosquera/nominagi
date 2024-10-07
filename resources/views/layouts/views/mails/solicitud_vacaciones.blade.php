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
<div class="alert alert-success" role="alert">
    <h4 class="alert-heading">{{ucfirst($empresa->nombre_empresa)}}, Notificacion de vacaciones</h4>
    <p>
        El usuario {{$person->first_name." ".$person->last_name}} ha solicitado vacaciones desde el {{$desde}} hasta el {{$hasta}}
    </p>
    <p> Puede aprobrar o no la misma ingresando al sistema como administrador </p>
</div>
</body>
</html>

