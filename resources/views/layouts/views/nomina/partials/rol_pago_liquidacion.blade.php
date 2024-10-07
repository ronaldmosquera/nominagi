<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header" >
                <div class="col-md-12 text-center">
                    <h3 class="box-title">
                        Rol de pago de liquidación {{$dataLiquidacion['nombreEmpleado']}}
                    </h3>
                </div>
                <div class="" style="margin-top: 20px">
                    @include('flash::message')
                </div>
                <div class="">
                    <section class="content">
                        <div class="row">
                            <div class="col-md-12" style="border: 1px solid #555555;border-radius: 5px">
                                <div style="text-align:center;background: #c3d69b;padding: 3px;font-weight: 600; margin: 15px 0px;" class="text-center">
                                          ROL DE LIQUIDACIÓN
                                      </div>
                                <table style="width: 100%;">
                                    <tr >
                                        <td style="text-align: left;font-size: 11pt;font-weight: 600">Nombre</td>
                                        <td style="text-align: right;font-size: 11pt;font-weight: 600">{{$dataLiquidacion['nombreEmpleado']}}</td>
                                    </tr>
                                    <tr >
                                        <td style="text-align: left;font-size: 11pt;font-weight: 600">Cargo</td>
                                        <td style="text-align: right;font-size: 11pt;font-weight: 600">{{$dataLiquidacion['cargo']}}</td>
                                    </tr>
                                    <tr >
                                        <td style="text-align: left;font-size: 11pt;font-weight: 600">{{ucwords($dataLiquidacion['documento'])}}</td>
                                        <td style="text-align: right;font-size: 11pt;font-weight: 600">{{$dataLiquidacion['identificacion']}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;font-size: 11pt;font-weight: 600">Fecha de liquidación</td>
                                        <td style="text-align: right;font-size: 11pt;font-weight: 600">
                                            {{$dataLiquidacion['fechaLiquidacion']}}
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
                                    </thead>
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
                                                        {{"$".number_format($dataLiquidacion['montoSalario'],2,".","")}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Horas extras
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{$dataLiquidacion['montoHorasExtras'] == "N/A" ? $dataLiquidacion['montoHorasExtras'] : "$".number_format($dataLiquidacion['montoHorasExtras'],2,".","")}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Comisiones
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{"$".number_format($dataLiquidacion['montoComisiones'],2,".","")}}
                                                    </td>
                                                </tr>
                                                @foreach($dataLiquidacion['arr_bonos_fijos'] as $bonosFijos)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ucfirst($bonosFijos->nombre) .", Calculado a ". $dataLiquidacion['diasTrabajadosMesActual']. " días"}}
                                                        </td>
                                                        <td class="text-center">
                                                            {{"+ $".number_format(($bonosFijos->monto/30)*$dataLiquidacion['diasTrabajadosMesActual'],2,".","")}}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Decimo tercero
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{$dataLiquidacion['montoDecimoTercerSueldo'] == "N/A" ? $dataLiquidacion['montoDecimoTercerSueldo'] : "$".number_format($dataLiquidacion['montoDecimoTercerSueldo'],2,".","")}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Decimo cuarto
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{$dataLiquidacion['montoDecimoCuartoSueldo'] == "N/A" ? $dataLiquidacion['montoDecimoCuartoSueldo'] : "$".number_format($dataLiquidacion['montoDecimoCuartoSueldo'],2,".","")}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Desahucio
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{$dataLiquidacion['montoDesahucio'] == "N/A" ? $dataLiquidacion['montoDesahucio'] : "$".number_format($dataLiquidacion['montoDesahucio'],2,".","")}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Despido Intempestivo
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{$dataLiquidacion['montoDespidoIntempestivo'] == "N/A" ? $dataLiquidacion['montoDespidoIntempestivo'] : "$".number_format($dataLiquidacion['montoDespidoIntempestivo'],2,".","")}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Bonificación 25%
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{$dataLiquidacion['bono25'] == "N/A" ? $dataLiquidacion['bono25'] : "$".number_format($dataLiquidacion['bono25'],2,".","")}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Despido ineficaz
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{$dataLiquidacion['despidoIneficaz'] == "N/A" ? $dataLiquidacion['despidoIneficaz'] : "$".number_format($dataLiquidacion['despidoIneficaz'],2,".","")}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Indemnización por discapacidad
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                        {{$dataLiquidacion['indemnizacionDiscapacidad'] == "N/A" ? $dataLiquidacion['indemnizacionDiscapacidad'] : "$".number_format($dataLiquidacion['indemnizacionDiscapacidad'],2,".","")}}
                                                    </td>
                                                </tr>
                                                @if(is_numeric($dataLiquidacion['terminacionAntesPlazo']))
                                                    <tr>
                                                        <td class="text-center">Indemnización por discapacidad</td>
                                                        <td class="text-center">${{$dataLiquidacion['terminacionAntesPlazo']}}</td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Total Ingresos
                                                    </td>
                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;border-top: 1px solid black">
                                                        {{"$".number_format($dataLiquidacion['montoTotalIngresos'],2,".","")}}
                                                    </td>
                                                </tr>

                                                </thead>
                                            </table>
                                        </td>
                                        <td>
                                            <table style="width: 100%">
                                                <tr >
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Consumos
                                                    </td>
                                                    <td class="text-center">
                                                        {{"$".number_format($dataLiquidacion['montoConsumos'],2,".","")}}
                                                    </td>
                                                </tr>
                                                <tr >
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Anticipos
                                                    </td>
                                                    <td class="text-center">
                                                        {{"$".number_format($dataLiquidacion['montoAnticipos'],2,".","")}}
                                                    </td>
                                                </tr>
                                                <tr >
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Otros descuentos
                                                    </td>
                                                    <td class="text-center">
                                                        {{"$".number_format($dataLiquidacion['montoDescuentos'],2,".","")}}
                                                    </td>
                                                </tr>
                                                @foreach($dataLiquidacion['arrPrestamos'] as $prestamo)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ucfirst($prestamo->nombre).", Total del adeudo"}}
                                                        </td>
                                                        <td class="text-center">
                                                            {{"- $".number_format(($prestamo->total-$prestamo->abonado),2,".","")}}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr >
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                        Total Egresos
                                                    </td>
                                                    <td class="text-center">
                                                        {{"$".number_format($dataLiquidacion['montoTotalEgresos'],2,".","")}}
                                                    </td>
                                                </tr>
                                                <tr >
                                                    <td>

                                                    </td>
                                                    <td class="text-center">
                                                            -
                                                    </td>
                                                </tr>
                                                <tr >
                                                    <td>

                                                    </td>
                                                    <td class="text-center" >
                                                            -
                                                    </td>
                                                <tr>
                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;border: 1px solid black">
                                                        Total a recibir
                                                    </td>
                                                    <td class="text-center" style="font-weight: 900;font-size: 12pt;border: 1px solid black">
                                                        {{"$".number_format($dataLiquidacion['montoTotalARecibir'],2,".","")}}
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="text-center" style="text-align:center;margin-top: 60px">
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
