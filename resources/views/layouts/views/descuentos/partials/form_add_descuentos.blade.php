<form id="form_descuentos" name="form_descuentos">
    <input type="hidden" id="id_descuento" value="{{isset($dataDescuentos->id_descuento) ? $dataDescuentos->id_descuento : ''}}">
    <input type="hidden" class="form-control" id="fd" name="fd" value="{{isset($dataDescuentos->fecha_descuento) ? $dataDescuentos->fecha_descuento:  ''}}">
    <input type="hidden" class="form-control" id="c" name="c" value="{{isset($dataDescuentos->cantidad) ? $dataDescuentos->cantidad : 1}}">
    <input type="hidden" class="form-control" id="d" name="d" value="{{isset($dataDescuentos->descripcion) ? $dataDescuentos->descripcion : ''}}">
    <input type="hidden" class="form-control" id="ide" name="ide" value="{{isset($dataDescuentos->id_empleado) ? $dataDescuentos->id_empleado : ''}}">
    <input type="hidden" class="form-control" id="nombre" name="nombre" value="{{isset($dataDescuentos->nombre) ? $dataDescuentos->nombre : ''}}">
    <input type="hidden" class="form-control" id="invoice_item_type_id" name="invoice_item_type_id" value="{{isset($dataDescuentos->invoice_item_type_id) ? $dataDescuentos->invoice_item_type_id : ''}}">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <div class="col-md-6" >
                <h3 class="box-title">Ingrese el descuento a asignar</h3>
            </div>
            @if(empty($dataDescuentos->id_descuento))
                <div class="col-md-6 text-right" >
                    <button type="button" class="btn btn-success" data-toggle="tooltip"
                            title="Agregar descuento" id="btn_add_inputs" onclick="add_inputs()">
                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    </button>
                    {{-- <button type="button" class="btn btn-danger" data-toggle="tooltip" onclick="delete_inputs()"
                            title="Eiminar comsión">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button> --}}
                </div>
            @endif
        </div>
        <div class="box-body">
            <div id="inputs_descuentos"></div>
            <div class="text-center" style="padding-top: 10px">
                <button type="button" class="btn btn-info" id="btn_store_descuentos" onclick="store_descuentos()">
                    <i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i> Guardar</button>
            </div>
        </div>
    </div>
</form>
<script>
    add_inputs();
</script>
<style scoped>
div.text-area label.error{
    left:20px!important
}
</style>
