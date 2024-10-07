@php ini_set('max_execution_time',600); @endphp
<div style="width:100%;overflow-x: auto">
    <table class="table" style="width: 100%;">
        <tr>
            @foreach(getMes('',true) as $m => $mes)
                @if(in_array($m,$meses,true))
                    <td class="text-center" style="border: 1px solid silver;vertical-align: bottom;padding: 0">
                    <b>{{strtoupper($mes)}}</b>
                    <table>
                        <tr>
                            <td nowrap style="text-align: center;border: 1px solid silver;padding: 0px 5px;">EMPLEADOS</td>
                            @foreach(arrItemsProyeccion() as $item)
                                <td nowrap style="vertical-align: bottom;border: 1px solid silver;padding: 0px 10px;text-align: center">
                                    {{$item}} <i class="fa fa-arrow-circle-up text-red" aria-hidden="true"></i> <i class="fa fa-arrow-circle-down text-green" aria-hidden="true"></i>
                                </td>
                            @endforeach
                        </tr>
                        @php
                            $totalSalario = 0.00;
                            $totalHorasLaboradas = 0.00;
                            $totalSueldoLaborado = 0.00;
                            $nHe50 = 0.00;
                            $nHe100 = 0.00;
                            $nHe50Ganado = 0.00;
                            $nHe100Ganado = 0.00;
                            $comsiones = 0.00;
                            $transporte= 0.00;
                            $movilizacion = 0.00;
                            $dmo3er= 0.00;
                            $dmo4to= 0.00;
                            $fondoReserva= 0.00;
                            $vacaciones= 0.00;
                            $aportePatronal= 0.00;
                            $aportePersonal= 0.00;
                            $prestamoIEES= 0.00;
                            $prestamoOficina= 0.00;
                            $anticipo= 0.00;
                            $consumo= 0.00;
                            $perdida= 0.00;
                            $otroDescuento= 0.00;
                        @endphp
                        @foreach(getProyeccion($proyeccion->id_proyeccion)->empleadoProyeccion as $x => $proyeccion)
                            <tr>
                                <td nowrap style="text-align: left;padding: 0px 5px;border: 1px solid silver">
                                    {{strtoupper(getEmpleado($proyeccion->id_empleado)->first_name)}}
                                </td>
                                @foreach($proyeccion->mesEmpleadoProyeccion as $mp =>$mesProyeccion)
                                    @php $z=1; @endphp
                                    @foreach($mesProyeccion->itemMesEmpleadoProyeccion as $l => $itemMesEmpleado)
                                        @if($m==$mesProyeccion->mes)
                                            @foreach(arrItemsProyeccion() as $i => $item)
                                                @php  @endphp
                                                @if($i == $itemMesEmpleado->id_item)
                                                    {{--@if($z!=$i)
                                                        @for($x=0;$x<=$i-1;$x++)
                                                            <td></td>
                                                        @endfor
                                                    @endif--}}
                                                    <td style="border: 1px solid silver">
                                                        <input type="number" onkeyup="calcular('{{$proyeccion->id_empleado}}','{{$itemMesEmpleado->id_item}}')" style="width: 100%;text-align: center;border: none" value="{{$itemMesEmpleado->valor}}">
                                                    </td>
                                                @endif
                                                @if($l == 0 && $i == 1)
                                                    @php $totalSalario += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 1 && $i == 1)
                                                    @php $totalHorasLaboradas += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 2 && $i == 1)
                                                    @php $totalSueldoLaborado += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 3 && $i == 1)
                                                    @php $nHe50 += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 4 && $i == 1)
                                                    @php $nHe100 += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 5 && $i == 1)
                                                    @php $nHe50Ganado += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 6 && $i == 1)
                                                    @php $nHe100Ganado += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 7 && $i == 1)
                                                    @php $comsiones += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 8 && $i == 1)
                                                    @php $transporte += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 9 && $i == 1)
                                                    @php $movilizacion += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 10 && $i == 1)
                                                    @php $dmo3er += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 11 && $i == 1)
                                                    @php $dmo4to += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 12 && $i == 1)
                                                    @php $fondoReserva += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 13 && $i == 1)
                                                    @php $vacaciones += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 14 && $i == 1)
                                                    @php $aportePatronal += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 15 && $i == 1)
                                                    @php $aportePersonal += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 16 && $i == 1)
                                                    @php $prestamoIEES += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 17 && $i == 1)
                                                    @php $prestamoOficina += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 18 && $i == 1)
                                                    @php $anticipo += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 19 && $i == 1)
                                                    @php $consumo += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 20 && $i == 1)
                                                    @php $perdida += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 21 && $i == 1)
                                                    @php $otroDescuento += $itemMesEmpleado->valor @endphp
                                                @endif
                                                @if($l == 22 && $i == 1)
                                                    @php $otroDescuento += $itemMesEmpleado->valor @endphp
                                                @endif
                                            @endforeach
                                        @endif
                                        @php $z++; @endphp
                                    @endforeach
                                @endforeach
                            </tr>
                        @if(count(getProyeccion($proyeccion->id_proyeccion)->empleadoProyeccion) == $x+1)
                            <tr>
                                <td style="text-align: left;padding: 0px 5px;">Total:</td>
                                <td style="border: 1px solid silver" id="total_salario_">{{$totalSalario}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$totalHorasLaboradas}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$totalSueldoLaborado}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$nHe50}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$nHe100}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$nHe50Ganado}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$nHe100Ganado}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$comsiones}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$transporte}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$movilizacion}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$dmo3er}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$dmo4to}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$fondoReserva}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$vacaciones}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$aportePatronal}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$aportePersonal}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$prestamoIEES}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$prestamoOficina}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$anticipo}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$consumo}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$perdida}}</td>
                                <td style="border: 1px solid silver" id="total_horas_laboradas">{{$otroDescuento}}</td>
                            </tr>
                            @endif
                        @endforeach

                    </table>
                </td>
                @endif
            @endforeach
        </tr>
    </table>
</div>