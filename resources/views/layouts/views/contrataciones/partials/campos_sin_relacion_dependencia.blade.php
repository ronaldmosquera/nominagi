<div class="col-md-3">
    <div class="form-group">
        <div class="input-group">
                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                    <i class="fa fa-file-text-o" aria-hidden="true"></i> TIPO DOCUMENTO
                </span>
            <select class="form-control" id="tipo_documento" name="tipo_documento" required onchange="select_retencion()">
                @foreach ($tipoDocumentos as $td)
                    <option {{isset($dataAddendum->tipo_documento) && $dataAddendum->tipo_documento == $td->invoice_type_id ? 'selected' : ''}}
                            value='{{$td->invoice_type_id}}'>
                        {{$td->description}}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="col-md-4" >
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                <i class="fa fa-check" aria-hidden="true"></i> TIPO RETENCIÓN IVA
            </span>
            <select class="form-control" id="tipo_impuesto_iva" name="tipo_impuesto_iva"
                    onchange="$('#retencion_iva').val($('#tipo_impuesto_iva').val().split('*')[0])">
                    <option value="">Seleccione</option>
                @foreach ($tipoImpuestos->filter(function($obj){ return $obj->tipo_impuesto== 'IVA';}) as $tipoIva)
                    <option {{isset($dataAddendum->deduction_type_id) && $dataAddendum->deduction_type_id == $td->deduction_type_id ? 'selected' : ''}}
                            value='{{$tipoIva->porcentaje_impuesto}}*{{$tipoIva->deduction_type_id}}*{{$tipoIva->gl_account_id}}*{{$tipoIva->tipo_impuesto}}'>
                        {{$tipoIva->description}}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="col-md-5" >
    <div class="form-group">
        <div class="input-group">
                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                    <i class="fa fa-check" aria-hidden="true"></i> TIPO RETENCIÓN RENTA
                </span>
            <select class="form-control" id="tipo_impuesto_renta" name="tipo_impuesto_renta"
                    onchange="$('#retencion_renta').val($('#tipo_impuesto_renta').val().split('*')[0])">
                    <option value="">Seleccione</option>
                @foreach ($tipoImpuestos->filter(function($obj){ return $obj->tipo_impuesto== 'RENTA';}) as $tipoRenta)
                    <option {{isset($dataAddendum->deduction_type_id) && $dataAddendum->deduction_type_id == $td->deduction_type_id ? 'selected' : ''}}
                            value='{{$tipoRenta->porcentaje_impuesto}}*{{$tipoRenta->deduction_type_id}}*{{$tipoRenta->gl_account_id}}*{{$tipoRenta->tipo_impuesto}}'>
                        {{$tipoRenta->description}}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="col-md-2">
    <div class="form-group">
        <div class="input-group">
                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                    <i class="fa fa-money" aria-hidden="true"></i> IVA
                </span>
                <select class="form-control" id="iva" name="iva" required>
                    <option selected disabled value="">Seleccione</option>
                    @foreach($iva as $i)
                        <option {{(isset($dataAddendum->iva) && $dataAddendum->iva == $i->valor) ? 'selected' : ''}}
                                value="{{$i->valor}}">{{$i->nombre}}</option>
                    @endforeach
                </select>
            {{--<input type="number"  class="form-control" id="iva" name="iva"
                   placeholder="Ej: 12%"  onkeypress="return filterFloat(event,this)" required>--}}
        </div>
    </div>
</div>
<div class="col-md-3" >
    <div class="form-group">
        <div class="input-group">
                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                    <i class="fa fa-money" aria-hidden="true"></i> RETENCIÓN IVA %
                </span>
            <input  type="number" class="form-control" id="retencion_iva" name="retencion_iva"
                    value="{{isset($dataAddendum->retencion_iva) ? $dataAddendum->retencion_iva : 0}}"
                    placeholder="Ej: 10%" required>
        </div>
    </div>
</div>
<div class="col-md-3" >
    <div class="form-group">
        <div class="input-group">
                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                    <i class="fa fa-money" aria-hidden="true"></i> RETENCIÓN RENTA %
                </span>
            <input  type="number" class="form-control" id="retencion_renta" name="retencion_renta"
                    value="{{isset($dataAddendum->retencion_renta) ? $dataAddendum->retencion_renta : 0}}"
                    placeholder="Ej: 10%" required>
        </div>
    </div>
</div>

<script>

    select_retencion()

    function select_retencion(){

        if($("#tipo_documento").val() == "INVOICE_HONORARIOS"){

            $("#tipo_impuesto_renta").attr('required',true)

        }else{

            $("#tipo_impuesto_renta").removeAttr('required')

        }

    }
</script>

