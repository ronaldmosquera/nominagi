<form id="form_consumo" name="form_consumo">
    <input type="hidden" id="id_consumo" value="{{isset($consumo->id_consumo) ? $consumo->id_consumo  : ""}}">
    <input type="hidden" id="id_empleado_consumo" name="id_empleado_consumo" value="{{isset($monto_pagado[0]->party_id) ? $monto_pagado[0]->party_id  : ""}}">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <div class="col-md-12">
                <h3 class="box-title">
                    Ingrese el consumo a solicitar
                </h3>
                {{--<div class="col-md-6 text-right">
                    <button type="button" class="btn btn-success btn-xs" id="btn_add_inputs" onclick="add_inputs()">
                       <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-xs" onclick="delete_inputs(this)">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                </div>--}}
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;">Empleado</span>
                        <input type="text" class="form-control" id="empleado" name="empleado" value="{{$monto_pagado[0]->first_name." ".$monto_pagado[0]->last_name}}" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;">Total $</span>
                        <input type="text" class="form-control" id="total" name="total" value="{{number_format($monto_pagado[0]->total,2,".","")}}" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;">Pagado $</span>
                        <input type="text" class="form-control" id="pagado" name="pagado" value="{{number_format($monto_pagado[0]->pagado,2,".","")}}" readonly>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: 20px">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;">Factura N#</span>
                        <input type="text" class="form-control" id="factura" name="factura" value="{{$monto_pagado[0]->invoice_number}}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;">Descuento</span>
                        <input type="date" class="form-control" id="fecha_descuento" name="fecha_descuento" value="{{isset($consumo->fecha_descuento) ? $consumo->fecha_descuento  : ""}}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;">A pagar</span>
                        <input type="text" class="form-control" id="a_pagar" name="a_pagar" value="{{isset($consumo->monto_descuento) ? $consumo->monto_descuento  : ""}}" required>
                    </div>
                </div>
            </div>
            {{--<div class="row" style="padding: 10px 0;">
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
                <div class="col-md-8" id="input_consumo">
                    @if(count($dataConsumo) > 0)
                        @foreach($dataConsumo as $key => $consumo)
                            <div class="row" id="row_{{$key+1}}" style="margin-bottom: 10px">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                                            Cantidad
                                        </span>
                                        <input type="number" onkeyup="calcular_total(this.id)" onclick="calcular_total(this.id)" min="1"
                                               class="form-control" id="cantidad_{{$key+1}}"
                                               value="{{$consumo->cantidad}}" @if($key+1 < count($dataConsumo)) {{"disabled='disabled'"}} @endif name="cantidad_{{$key+1}}" required>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                                            Producto
                                        </span>
                                        <select class="form-control" id="id_producto_{{$key+1}}" name="id_producto_{{$key+1}}" @if($key+1 < count($dataConsumo)) {{"disabled='disabled'"}}  @endif
                                                onchange="calcular_total(this.id)" required>
                                            <option disabled selected>Seleccione</option>
                                            @foreach($dataProductos as $productos)
                                                <option @if(!empty($consumo->id_producto)) @if($consumo->id_producto == $productos->id_productos) {{"selected='selected'"}} @else {{""}} @endif @endif
                                                        value="{{$productos->id_productos}}">{{$productos->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="costo_producto_{{$key+1}}" name="costo_producto_{{$key+1}}" value="{{$consumo->costo*$consumo->cantidad}}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="col-md-4">
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
            </div>--}}
            <div class="row">
                <div class="col-md-12">
                    <div style="padding-top: 10px">
                        <div class="col-md-10" style="padding: 0;">
                            <span id="msg" class="error"></span>
                        </div>
                        <div class="col-md-2 text-right" style="padding: 0;margin-top: 20px">
                            <button type="button" class="btn btn-info pull-right" id="btn_store_vacaciones"
                                    onclick="store_consumo('{{$monto_pagado[0]->invoice_id}}')">
                                <i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i>
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script>$('[data-toggle="tooltip"]').tooltip(); </script>