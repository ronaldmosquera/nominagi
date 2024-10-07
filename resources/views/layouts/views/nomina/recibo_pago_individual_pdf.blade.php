<!DOCTYPE html>
<html>
<head>
    <title>Roles de pagos</title>
    <style>
        .row {
            margin-right: -15px;
            margin-left: -15px;
        }
        .col-md-10 {
            width: 83.33333333%;
            position: relative;
            min-height: 1px;
            padding-right: 15px;
            padding-left: 15px;
        }
        .col-md-offset-1 {
            margin-left: 8.33333333%;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header" >
                    <div class="" style="margin-bottom: 50px">
                        <section class="content">
                            <div class="row" style="margin-top: 40px">
                                <div class="col-md-8 col-md-offset-2" style="border: 1px solid #555555;margin-bottom: 35px;border-radius: 5px">
                                    <div style="background: #c3d69b;padding: 3px;font-weight: 600; margin: 15px 0px;" class="text-center">
                                        @php
                                            $tipoContrato = \App\Models\Contrataciones::where('id_contrataciones',$dataRolIndividual['id_contratacion'])
                                             ->join('tipo_contrato as tp','tp.id_tipo_contrato','contrataciones.id_tipo_contrato')->select('relacion_dependencia')->first();
                                       @endphp
                                       @if($tipoContrato->relacion_dependencia)
                                            {{"ROL DE PAGOS"}}
                                       @else
                                            {{"PAGO DE SERIVICIOS"}}
                                       @endif
                                    </div>
                                    <table style="width: 100%;">
                                        <tr >
                                            <td style="text-align: left;font-size: 11pt;font-weight: 600">Nombre</td>
                                            <td style="text-align: right;font-size: 11pt;font-weight: 600">{{$dataRolIndividual['nombre_empleado']}}</td>
                                        </tr>
                                        <tr >
                                            <td style="text-align: left;font-size: 11pt;font-weight: 600">Cargo</td>
                                            <td style="text-align: right;font-size: 11pt;font-weight: 600">{{$dataRolIndividual['cargo']}}</td>
                                        </tr>
                                        <tr >
                                            <td style="text-align: left;font-size: 11pt;font-weight: 600">{{ucwords($dataRolIndividual['documento'])}}</td>
                                            <td style="text-align: right;font-size: 11pt;font-weight: 600">{{$dataRolIndividual['identificacion']}}</td>
                                        </tr>
                                        <tr >
                                            <td style="text-align: left;font-size: 11pt;font-weight: 600">Nómina</td>
                                            <td style="text-align: right;font-size: 11pt;font-weight: 600">
                                                @if(!$dataRolIndividual['consecutivos'])
                                                {{getMes(intval(\Carbon\Carbon::now()->format('m')))}} del {{\Carbon\Carbon::now()->format('Y')}}
                                                @else
                                                    {{getMes(intval(\Carbon\Carbon::parse($dataRolIndividual['fecha'])->format('m')))}} del {{\Carbon\Carbon::parse($dataRolIndividual['fecha'])->format('Y')}}
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                    <table style="width: 100%;margin: 20px 0px">
                                        <thead>
                                            <tr class="text-center">
                                                <td style="font-weight: 900;font-size: 11pt;border: 1px solid black">
                                                    INGRESOS
                                                </td>
                                                <td style="font-weight: 900;font-size: 11pt;border: 1px solid black">
                                                    EGRESOS
                                                </td>
                                            </tr>
                                            <tbody style="border: 1px solid black">
                                                <tr>
                                                    <td>
                                                        <table style="width: 100.5%;">
                                                            <thead>
                                                            <tr>
                                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                    Salario
                                                                </td>
                                                                <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                    {{"$".$dataRolIndividual['salario']}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                    Horas extras
                                                                </td>
                                                                <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                    {{"$".$dataRolIndividual['horas_extras']}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                    Comisiones
                                                                </td>
                                                                <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                    {{"$".$dataRolIndividual['comisiones']}}
                                                                </td>
                                                            </tr>
                                                            @foreach($dataRolIndividual['arr_bonos_fijos'] as $bonosFijos)
                                                                <tr>
                                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                        {{ucfirst($bonosFijos->nombre)}}
                                                                    </td>
                                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                        {{"$".number_format($bonosFijos->monto,2,".","")}}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            @if($dataRolIndividual['iva'] != false)
                                                                <tr>
                                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                        Iva
                                                                    </td>
                                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                        {{"$".number_format($dataRolIndividual['iva'],2,".","")}}
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                            <tr>
                                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                    Decimo tercero
                                                                </td>
                                                                <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                    {{$dataRolIndividual['decimo_tercero'] == "N/A" ? $dataRolIndividual['decimo_tercero'] : "$".number_format($dataRolIndividual['decimo_tercero'],2,".","")}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                    Decimo cuarto
                                                                </td>
                                                                <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                    {{$dataRolIndividual['decimo_cuarto'] == "N/A" ? $dataRolIndividual['decimo_cuarto'] : "$".number_format($dataRolIndividual['decimo_cuarto'],2,".","")}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                    Fondo reserva
                                                                </td>
                                                                <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                    {{$dataRolIndividual['fondo_reserva'] == "N/A" ? $dataRolIndividual['fondo_reserva'] : "$".$dataRolIndividual['fondo_reserva']}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                    Total Ingresos
                                                                </td>
                                                                <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;border-top: 1px solid black">
                                                                    {{"$".number_format($dataRolIndividual['ingresos'],2,".","")}}
                                                                </td>
                                                            </tr>
                                                            </thead>
                                                        </table>
                                                    </td>
                                                    <td>
                                                        <table style="width: 100%">
                                                            <tr >
                                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                    Aporte personal al IESS
                                                                </td>
                                                                <td class="text-center">
                                                                    {{$dataRolIndividual['aporte_personal_IESS'] == "N/A" ? $dataRolIndividual['aporte_personal_IESS'] : "$".$dataRolIndividual['aporte_personal_IESS']}}
                                                                </td>
                                                            </tr>
                                                            <tr >
                                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                    Consumos
                                                                </td>
                                                                <td class="text-center">
                                                                    {{"$".$dataRolIndividual['consumos']}}
                                                                </td>
                                                            </tr>
                                                            @foreach($dataRolIndividual['arrPrestamos'] as $prestamos)
                                                                <tr>
                                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                        {{ucfirst($prestamos->nombre)}}
                                                                    </td>
                                                                    <td class="text-center" style="font-size: 11pt;padding:0px 5px;">
                                                                        {{"$".number_format($prestamos->cuota,2,".","")}}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            @if($dataRolIndividual['retencionIva'] != false)
                                                                <tr>
                                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                        Retención iva
                                                                    </td>
                                                                    <td class="text-center" style="font-size: 11pt;padding:0px 5px;">
                                                                        {{"$".number_format($dataRolIndividual['retencionIva'],2,".","")}}
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                            @if($dataRolIndividual['retencionRenta'] != false)
                                                                <tr>
                                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                        Retención renta
                                                                    </td>
                                                                    <td class="text-center" style="font-size: 11pt;padding:0px 5px;">
                                                                        {{"$".number_format($dataRolIndividual['retencionRenta'],2,".","")}}
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                            <tr>
                                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                    Anticipos
                                                                </td>
                                                                <td class="text-center">
                                                                    {{"$".$dataRolIndividual['anticipos']}}
                                                                </td>
                                                            </tr>
                                                            @if(count($dataRolIndividual['otros_descuentos'])>0)
                                                                <tr >
                                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                        Otros descuentos
                                                                        <ul style="padding-left: 5px;margin: 0;">
                                                                            @foreach($dataRolIndividual['otros_descuentos'] as $descuentoNombre)
                                                                                <li style="list-style: none">
                                                                                    <table style="width: 100%;">
                                                                                        <tr>
                                                                                            <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                                                {{$descuentoNombre->nombre}}
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <table style="width: 100%;">
                                                                            <tr>
                                                                                <td>
                                                                                    <ul style="margin: 0;padding: 0;">
                                                                                        @foreach($dataRolIndividual['otros_descuentos'] as $key => $descuentoMonto)
                                                                                            <li style="list-style: none;">
                                                                                                <table style="width: 100%;">
                                                                                                    <tr>
                                                                                                        <td {{$key == 0 ? "style=padding-top:17px" : ""}}>
                                                                                                            {{"$".number_format($descuentoMonto->cantidad,2,".","")}}
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </li>
                                                                                        @endforeach
                                                                                    </ul>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                            <tr >
                                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                    Total Egresos
                                                                </td>
                                                                <td class="text-center">
                                                                    {{"$".number_format($dataRolIndividual['egresos'],2,".","")}}
                                                                </td>
                                                            </tr>
                                                            <tr >
                                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;border: 1px solid black">
                                                                    Total a recibir
                                                                </td>
                                                                <td class="text-center" style="font-weight: 900;font-size: 12pt;border: 1px solid black">
                                                                    {{"$".number_format($dataRolIndividual['total'],2,".","")}}
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </thead>
                                    </table>
                                    <div class="text-center" style="margin-top: 60px">
                                        <P>___________________________</P>
                                        <p style="font-size: 11pt;font-weight: 800"> FIRMA</p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
