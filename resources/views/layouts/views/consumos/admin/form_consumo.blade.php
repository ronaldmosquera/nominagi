<form id="form_consumo" name="form_consumo">
    <input type="hidden" id="id_consumo" value="{{!empty($dataConsumo[0]->id_consumo) ? $dataConsumo[0]->id_consumo : ''}}">
    <input type="hidden" id="iva" value="{{!empty($iva->iva) ? $iva->iva : ''}}">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <div class="col-md-12">
                <div class="col-md-12">
                    <h3 class="box-title">
                        {{"Solicitu de consumo del empleado:" .$nombre_empleado}}
                    </h3>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="row" style="padding: 10px 0;">
                <div class="col-md-6">
                    <div class="input-group" data-toggle="tooltip" title="Diferir a:">
                        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                            Diferir a
                        </span>
                        <select class="form-control" id="id_diferir" required>
                            <option disabled selected> Seleccione </option>
                            @for($i=1; $i<=$meses_a_diferir->diferir_consumos_meses; $i++)
                                <option @if(!empty($dataConsumo[0]->meses_diferir)) @if($dataConsumo[0]->meses_diferir == $i) {{"selected='selected'"}} @else {{""}} @endif @endif
                                        value="{{$i}}"> @php if($i<2) $p =''; else $p ='es'; @endphp  {{$i . " mes".$p}}  </option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group" id="div_fecha_diferir" data-toggle="tooltip" data-toggle="tooltip"
                         title="Fecha en la que se empezará a descontar el anticipo">
                        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                            Apartir de:
                        </span>
                        <input type="date" name="fecha_diferir" id="fecha_diferir" onchange="verificar_fecha()"
                               value="{{ !empty($dataConsumo[0]->fecha_inicio_diferir) ? $dataConsumo[0]->fecha_inicio_diferir : ''}}"
                               class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="row" style="padding: 10px 0;">
                <div class=" col-md-6 col-md-offset-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">Descripción del consumo</div>
                        <div class="panel-body">
                            <table>
                                <tbody id="tbody_descripcion_producto">
                                @if(count($dataConsumo) > 0)
                                    @php $subTotal  = 0; @endphp
                                    @foreach($dataConsumo as $key => $consumo)
                                        <tr>
                                            <td id="td_producto_"{{$key+1}} style="width: 100%;">
                                                {{ucwords($consumo->nombre)}} x {{strtoupper($consumo->cantidad)}}
                                            </td>
                                            <td class="text-right" id="td_costo_{{$key+1}}">
                                                {{"$".number_format($consumo->costo*$consumo->cantidad,2)}}
                                            </td>
                                        </tr>
                                        @php $subTotal += $consumo->costo*$consumo->cantidad; @endphp
                                    @endforeach
                                @endif
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
                                        {{!empty($dataConsumo[0]->total) ? "$".number_format($dataConsumo[0]->total - (($subTotal*$iva->iva)/100),2) : ''}}
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
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div style="padding-top: 10px" class="text-center">
                        <button type="button" class="btn btn-info" id="btn_store_vacaciones"
                                onclick="store_consumo_admin()">
                            <i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i>
                            Guardar
                        </button>

                    </div>
                </div>
            </div>

        </div>
    </div>
</form>
<script>$('[data-toggle="tooltip"]').tooltip(); </script>