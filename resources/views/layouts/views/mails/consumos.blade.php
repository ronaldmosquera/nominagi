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
        .col-md-6 {
            width: 50%;
        }
        .col-md-offset-3 {
            margin-left: 25%;
        }
        .panel {
            margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid transparent;
            border-radius: 4px;
            -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
            box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
            border: 1px solid silver;
        }
        .panel-default > .panel-heading {
            background-color: #ddd;
            border-bottom: 1px solid silver;
            color: #000;
         }
        .panel-body {
            padding: 15px;

        }
        .text-right {
            text-align: right;
        }
        td{
            color:black
        }
        @media(max-width: 500px){
            .col-md-6 {
                width: 100%;
            }

            .col-md-offset-3 {
                margin-left: 0%;
            }
        }
    </style>
</head>
<body>
<div class="alert alert-{!! $estado == 1 ? "success" : "warning"!!}" role="alert">
<h4 class="alert-heading">{!! $estado == 1 ? "Muy bien ".$nombreEmpleado->first_name." ".$nombreEmpleado->last_name."   !" : "Oooooooh! :("!!}</h4>
<p>
    @if($estado == 1)
    Tu consumo solicitado en fecha {{$dataConsumo[0]->fecha_solicitud}} ha sido aprobado y se detalla a continucación:
    @else
    Lo sentimos tu consumo no ha sido aprobado, el motivo será descrito en el siguiente comentario del administrador: <br />{{$message1}}.
    @endif
</p>
<p>
    @if($estado == 1)
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading panel-body">Descripción del consumo</div>
            <div class="panel-body">
                <table>
                    <tbody id="tbody_descripcion_producto">
                    @php $subTotal  = 0; @endphp
                        @foreach($dataConsumo as $key => $consumo)
                            <tr>
                                <td  style="width: 100%;">
                                    {{ucwords($consumo->nombre)}} x {{strtoupper($consumo->cantidad)}}
                                </td>
                                <td class="text-right">
                                    {{"$".number_format($consumo->costo*$consumo->cantidad,2)}}
                                </td>
                            </tr>
                            @php $subTotal += $consumo->costo*$consumo->cantidad; @endphp
                        @endforeach
                    </tbody>
                </table>
                <hr/>
                <table style="margin-top: 15px">
                    <tbody>
                    <tr>
                        <td style="width: 100%;">
                            Sub total:
                        </td>
                        <td class="text-right" style="width: 50%;" id="sub_total">
                            {{!empty($dataConsumo[0]->total) ? "$".number_format(($dataConsumo[0]->total-(($subTotal*$iva->iva)/100)),2) : ''}}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 100%;">
                            IVA: {{$iva->iva. "%"}}
                        </td>
                        <td class="text-right" style="width: 50%;" id="td_iva" >
                            {{!empty($dataConsumo[0]->total) ? "$".number_format(($subTotal*$iva->iva)/100,2) : ''}}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 100%;">
                            Total:
                        </td>
                        <td class="text-right" style="width: 50%;" id="total">
                            {{!empty($dataConsumo[0]->total) ? "$".number_format($dataConsumo[0]->total,2) : ''}}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</p>
<hr>
<p class="mb-0">
    @if($estado == 1)
    Disfruta de tu consumo...!
    @else
    Para mayor información por favor dirigirse a la administración de la sede.
    @endif
    <br />  Correo enviado a {{$mailEmpleado}}
</p>
</div>
</body>
</html>

