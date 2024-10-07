<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header">
                <div class="col-md-4">
                <h3 class="box-title" style="margin-top: 9px">Liquidación {{ucwords($dataLiquidacion['nombreEmpleado'])}}</h3>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon3" style="border:none"><b>Fecha de terminación</b></span>
                        <input type="date" id="fecha_terminacion" name="fecha_terminacion" value="{{$fechaTerminacion}}"
                               class="form-control" style="border:none;background:transparent" disabled>
                    </div>
                    <input type="checkbox" id="bono_25_porciento" name="bono_25_porciento" {{$bono_25_porciento ? 'checked' : ''}} class="hide" >
                </div>
                <div class="col-md-3 text-right" id="btn-cancel-contrato">
                    <button class="btn btn-success" onclick="store_terminar_contratatacion('{{$dataLiquidacion['idContrato']}}',1,'{{$id_motivo_anulacion}}')">
                        <i class="fa fa-cog" aria-hidden="true" id="cog"></i> Generar liquidación
                    </button>
                </div>
             </div>
             <!-- /.box-header -->
             <div class="box-body table-responsive no-padding overflow-auto" style="height:500px">
                    <table class="table table-striped" id="tabla_datos_liquidacion">
                        <tr>
                            <th class="text-center">Rubro</th>
                            <th class="text-center">Monto</th>
                        </tr>
                        <tr>
                            <td class="text-center">Bonificación 25%</td>
                            <td class="text-center" id="bono25">
                                @if(is_numeric($dataLiquidacion['bono25']))
                                    <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{$dataLiquidacion['bono25']}}">
                                @else
                                    {{$dataLiquidacion['bono25']}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">Despido por visto bueno</td>
                            <td class="text-center" id="vistoBueno">
                                @if (is_numeric($dataLiquidacion['vistoBueno']))
                                    <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{$dataLiquidacion['vistoBueno']}}">
                                @else
                                    {{$dataLiquidacion['vistoBueno']}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">Despido ineficaz</td>
                            <td class="text-center" id="despidoIneficaz">
                                @if (is_numeric($dataLiquidacion['despidoIneficaz']))
                                    <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()"  min="0" value="{{$dataLiquidacion['despidoIneficaz']}}">
                                @else
                                    {{$dataLiquidacion['despidoIneficaz']}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">Indemnización por discapacidad</td>
                            <td class="text-center" id="indemnizacionDiscapacidad">
                                @if (is_numeric($dataLiquidacion['indemnizacionDiscapacidad']))
                                    <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{$dataLiquidacion['indemnizacionDiscapacidad']}}">
                                @else
                                    {{$dataLiquidacion['indemnizacionDiscapacidad']}}
                                @endif
                            </td>
                        </tr>
                        @if(is_numeric($dataLiquidacion['terminacionAntesPlazo']))
                            <tr>
                                <td class="text-center">Indemnización por discapacidad</td>
                                <td class="text-center" id="terminacionAntesPlazo">
                                    <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{$dataLiquidacion['terminacionAntesPlazo']}}">
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="text-center">Decimo tercer sueldo</td>
                            <td class="text-center" id="montoDecimoTercerSueldo">
                                @if (is_numeric($dataLiquidacion['montoDecimoTercerSueldo']))
                                    <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{$dataLiquidacion['montoDecimoTercerSueldo']}}">
                                @else
                                    {{$dataLiquidacion['montoDecimoTercerSueldo']}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">Decimo cuarto sueldo</td>
                            <td class="text-center" id="montoDecimoCuartoSueldo">
                                @if (is_numeric($dataLiquidacion['montoDecimoCuartoSueldo']))
                                    <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{$dataLiquidacion['montoDecimoTercerSueldo']}}">
                                @else
                                    {{$dataLiquidacion['montoDecimoCuartoSueldo']}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">Vacaciones</td>
                            <td class="text-center" id="montoVacaciones">
                                @if (is_numeric($dataLiquidacion['montoVacaciones']))
                                    <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{$dataLiquidacion['montoVacaciones']}}">
                                @else
                                    {{$dataLiquidacion['montoVacaciones']}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">Desahucio</td>
                            <td class="text-center" id="montoDesahucio">
                                @if (is_numeric($dataLiquidacion['montoDesahucio']))
                                    <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{$dataLiquidacion['montoDesahucio']}}">
                                @else
                                    {{$dataLiquidacion['montoDesahucio']}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">Despido Intempestivo</td>
                            <td class="text-center" id="montoDespidoIntempestivo">
                                @if (is_numeric($dataLiquidacion['montoDespidoIntempestivo']))
                                    <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{$dataLiquidacion['montoDespidoIntempestivo']}}">
                                @else
                                    {{$dataLiquidacion['montoDespidoIntempestivo']}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">Horas Extras</td>
                            <td class="text-center" id="montoHorasExtras">
                                <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{$dataLiquidacion['montoHorasExtras']}}">
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">Comisiones</td>
                            <td class="text-center" id="montoComisiones">
                                <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{$dataLiquidacion['montoComisiones']}}">
                            </td>
                        </tr>
                        @foreach($dataLiquidacion['arr_bonos_fijos'] as $bonosFijos)
                            <tr>
                                <td class="text-center">
                                    {{ucfirst($bonosFijos->nombre) .", Calculado a ". $dataLiquidacion['diasTrabajadosMesActual']. " días"}}
                                </td>
                                <td class="text-center bonos_fijos">
                                    {{"+ $".number_format(($bonosFijos->monto/30)*$dataLiquidacion['diasTrabajadosMesActual'],2,".","")}}
                                </td>
                            </tr>
                        @endforeach
                        @foreach($dataLiquidacion['arrPrestamos'] as $prestamo)
                            <tr>
                                <td class="text-center" cla>
                                    {{ucfirst($prestamo->nombre).", Total del adeudo"}}
                                </td>
                                <td class="text-center prestamos">
                                    {{"- $".number_format(($prestamo->total-$prestamo->abonado),2,".","")}}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td class="text-center">Consumos</td>
                            <td class="text-center" id="montoConsumos">
                                <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{$dataLiquidacion['montoConsumos']}}">
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">Descuentos</td>
                            <td class="text-center" id="montoDescuentos">
                                <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{$dataLiquidacion['montoDescuentos']}}">
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">Aporte personal IESS</td>
                            <td class="text-center" id="aportePersonal">
                                @if (is_numeric($dataLiquidacion['aportePersonal']))
                                    <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{$dataLiquidacion['aportePersonal']}}">
                                @else
                                    {{$dataLiquidacion['aportePersonal']}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">Anticipos</td>
                            <td class="text-center" id="montoAnticipos">
                                <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{$dataLiquidacion['montoAnticipos']}}">
                            </td>
                        </tr>
                            <tr>
                                <td class="text-center">Iva</td>
                                <td class="text-center" id="iva">
                                    @if (is_numeric($dataLiquidacion['retencionIva']))
                                        <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{number_format($dataLiquidacion['iva'],2)}}">
                                    @else
                                        {{$dataLiquidacion['iva']}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">Retención Iva</td>
                                <td class="text-center" id="retencionIva">
                                    @if (is_numeric($dataLiquidacion['retencionIva']))
                                        <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{number_format($dataLiquidacion['iva'],2)}}">
                                    @else
                                        {{$dataLiquidacion['retencionIva']}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">Retención renta</td>
                                <td class="text-center" id="retencionRenta">
                                    @if (is_numeric($dataLiquidacion['retencionRenta']))
                                        <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{number_format($dataLiquidacion['retencionRenta'],2)}}">
                                    @else
                                        {{$dataLiquidacion['retencionRenta']}}
                                    @endif
                                </td>
                            </tr>
                        <tr>
                            <td class="text-center">Salario</td>
                            <td class="text-center" id="montoSalario">
                                <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" min="0" value="{{number_format($dataLiquidacion['montoSalario'],2,".","")}}">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-center" style="font-weight: bold; background: #00ca6d;color: white;font-size: 12pt">
                               Total a liquidar :<span id="montoTotalARecibir">{{number_format($dataLiquidacion['montoTotalARecibir'],2,".","")}}</span>
                               {{-- <input type="number" class="form-control form-control-sm value-input" onkeyup="calcula_monto_a_recibir()" onchange="calcula_monto_a_recibir()" style="margin: 0px 0px 0 38px;" min="0" value="{{number_format($dataLiquidacion['montoTotalARecibir'],2,".","")}}"> --}}
                            </td>
                        </tr>
                    </table>
             </div>
                <!-- /.box-body -->
         </div>
            <!-- /.box -->
     </div>
 </div>
<style>
    .value-input{
        width: 100px;
        margin: 0 auto;
        text-align: center
    }
</style>
