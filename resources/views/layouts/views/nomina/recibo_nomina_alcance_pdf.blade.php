<!DOCTYPE html>
<html>
<head>
    <title>Alcance de nómina</title>
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
                                        ALCANCE DE NÓMINA
                                    </div>
                                    <table style='width:100%'>
                                        <tr>
                                            <td style="text-align: left;font-size: 11pt;font-weight: 600">Nombre</td>
                                            <td style="text-align: right;font-size: 11pt;font-weight: 600">{{$dataRolIndividual['nombre_empleado']}}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left;font-size: 11pt;font-weight: 600">Cargo</td>
                                            <td style="text-align: right;font-size: 11pt;font-weight: 600">{{$dataRolIndividual['cargo']}}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left;font-size: 11pt;font-weight: 600">{{ucwords($dataRolIndividual['documento'])}}</td>
                                            <td style="text-align: right;font-size: 11pt;font-weight: 600">{{$dataRolIndividual['identificacion']}}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left;font-size: 11pt;font-weight: 600">Nómina</td>
                                            <td style="text-align: right;font-size: 11pt;font-weight: 600">
                                                {{getMes(intval($dataRolIndividual['fecha_nomina']->format('m')))}} del {{$dataRolIndividual['fecha_nomina']->format('Y')}}
                                            </td>
                                        </tr>
                                    </table>
                                    <table style="width: 100%;margin: 20px 0px">
                                        <thead>
                                            <tr class="text-center">
                                                <td style="font-weight: 900;font-size: 11pt;border: 1px solid black" colspan="2">
                                                    DETALLES
                                                </td>
                                            </tr>
                                            <tbody style="border: 1px solid black">
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
                                                        Iva
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{"$".$dataRolIndividual['iva']}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Retención iva
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{"$".$dataRolIndividual['retencion_iva']}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Retención renta
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{"$".$dataRolIndividual['retencion_renta']}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Aporte personal
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{"$".$dataRolIndividual['aporte_personal']}}
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
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Bonos
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{"$".$dataRolIndividual['bono']}}
                                                    </td>
                                                </tr>
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
                                                        {{"$".number_format($dataRolIndividual['total'],2,".","")}}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </thead>
                                    </table>
                                    <div style="margin-top: 20px;padding-left:15px">
                                        <p><b>Notas:</b> {{$dataRolIndividual['comentario']}}</p>
                                    </div>
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
