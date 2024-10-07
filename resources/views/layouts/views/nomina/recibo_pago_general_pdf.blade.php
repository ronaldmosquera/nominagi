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
<div class="" style="">
    <section class="content">
        <div class="row"style="margin-top: 40px">
            <section class="content">
                <div class="row" style="margin-top: 40px">
                    @foreach($dataVistaRolesGeneral as $key => $nomina)
                        <div class="col-md-10 col-md-offset-1" style="border: 1px solid #555555;margin-bottom: 35px;border-radius: 5px;margin-top: 100px">
                            <div style="background: #c3d69b;padding: 3px;font-weight: 600; margin: 15px 0px;" class="text-center">
                                @php
                                    $tipoContrato = \App\Models\Contrataciones::where('id_contrataciones',$nomina['id_contratacion'])
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
                                    <td style="text-align: right;font-size: 11pt;font-weight: 600">{{$nomina['nombre_empleado']}}</td>
                                </tr>
                                <tr >
                                    <td style="text-align: left;font-size: 11pt;font-weight: 600">Cargo</td>
                                    <td style="text-align: right;font-size: 11pt;font-weight: 600">{{$nomina['cargo']}}</td>
                                </tr>
                                <tr >
                                    <td style="text-align: left;font-size: 11pt;font-weight: 600">{{ucwords($nomina['documento'])}}</td>
                                    <td style="text-align: right;font-size: 11pt;font-weight: 600">{{$nomina['identificacion']}}</td>
                                </tr>
                                <tr >
                                    <td style="text-align: left;font-size: 11pt;font-weight: 600">Nómina</td>
                                    <td style="text-align: right;font-size: 11pt;font-weight: 600">
                                        {{getMes(intval(\Carbon\Carbon::now()->subMonth(1)->format('m')))}} del {{\Carbon\Carbon::now()->format('m') == 01
                                                     ? \Carbon\Carbon::now()->subYear(1)->format('Y')
                                                     : \Carbon\Carbon::now()->format('Y')}}
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
                                                    @if($tipoContrato->relacion_dependencia)
                                                        Salario
                                                    @else
                                                        Base
                                                    @endif
                                                </td>
                                                <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                    {{"$".$nomina['salario']}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                    @if($tipoContrato->relacion_dependencia)
                                                        Horas extras
                                                    @else
                                                        TRAX Adicionales
                                                    @endif
                                                </td>
                                                <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                    {{"$".$nomina['horas_extras']}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                    Comisiones
                                                </td>
                                                <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                    {{"$".$nomina['comisiones']}}
                                                </td>
                                            </tr>
                                            @foreach($nomina['arr_bonos_fijos'] as $bonosFijos)
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        {{ucfirst($bonosFijos->nombre)}}
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{"$".number_format($bonosFijos->monto,2,".","")}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if($nomina['iva'] != false)
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Iva
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{"$".number_format($nomina['iva'],2,".","")}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($tipoContrato->relacion_dependencia)
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Decimo tercero
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{$nomina['decimo_tercero'] == "N/A" ? $nomina['decimo_tercero'] : "$".$nomina['decimo_tercero']}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Decimo cuarto
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{$nomina['decimo_cuarto'] == "N/A" ? $nomina['decimo_cuarto'] : "$".$nomina['decimo_cuarto']}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Fondo reserva
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{$nomina['fondo_reserva'] == "N/A" ? $nomina['fondo_reserva'] : "$".$nomina['fondo_reserva']}}
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                    Total Ingresos
                                                </td>
                                                <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;border-top: 1px solid black">
                                                    {{"$".$nomina['ingresos'] }}
                                                </td>
                                            </tr>
                                            </thead>
                                        </table>
                                    </td>
                                    <td>
                                        <table style="width: 100%">
                                            @if($tipoContrato->relacion_dependencia)
                                                <tr >
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Aporte personal al IESS
                                                    </td>
                                                    <td class="text-center">
                                                        {{$nomina['aporte_personal_IESS'] == "N/A" ? $nomina['aporte_personal_IESS'] : "$".$nomina['aporte_personal_IESS']}}
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr >
                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                    Consumos
                                                </td>
                                                <td class="text-center">
                                                    {{"$".$nomina['consumos']}}
                                                </td>
                                            </tr>
                                            @foreach($nomina['arrPrestamos'] as $prestamos)
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        {{ucfirst($prestamos->nombre)}}
                                                    </td>
                                                    <td class="text-center">
                                                        {{"$".number_format($prestamos->monto,2,".","")}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if($nomina['retencionIva'] != false)
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Retención iva
                                                    </td>
                                                    <td class="text-center" >
                                                        {{"$".number_format($nomina['retencionIva'],2,".","")}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($nomina['retencionRenta'] != false)
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Retención renta
                                                    </td>
                                                    <td class="text-center">
                                                        {{"$".number_format($nomina['retencionRenta'],2,".","")}}
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr >
                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                    Anticipos
                                                </td>
                                                <td class="text-center">
                                                    {{"$".$nomina['anticipos']}}
                                                </td>
                                            </tr>
                                            @if(count($nomina['otros_descuentos'])>0)
                                                <tr >
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Otros descuentos
                                                        <ul style="padding-left: 5px;margin: 0;">
                                                            @foreach($nomina['otros_descuentos'] as $descuentoNombre)
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
                                                                        @foreach($nomina['otros_descuentos'] as $x => $descuentoMonto)
                                                                            <li style="list-style: none;">
                                                                                <table style="width: 100%;">
                                                                                    <tr>
                                                                                        <td {{$x == 0 ? "style=padding-top:17px" : ""}}>
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
                                                    {{"$".$nomina['Egresos']}}
                                                </td>
                                            </tr>
                                            <tr >
                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;border: 1px solid black">
                                                    Total a recibir
                                                </td>
                                                <td class="text-center" style="font-weight: 900;font-size: 12pt;border: 1px solid black">
                                                    {{"$".$nomina['total']}}
                                                </td>
                                            </tr>
                                            {{--<tr>
                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                    Beneficio : Aporte patronal al IESS
                                                </td>
                                                <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;border-top: 1px solid black">
                                                    {{"$".$nomina['aporte_patronal_IEES'] }}
                                                </td>
                                            </tr>--}}
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
                        @if($key+1 < count($dataVistaRolesGeneral))
                            <div style='page-break-after:always;'></div>
                        @endif
                    @endforeach
                </div>
            </section>
        </div>
    </section>
</div>
</body>
</html>
