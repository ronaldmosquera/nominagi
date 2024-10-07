<form id="form_comsiones" name="form_comsiones">
    <input type="hidden" id="id_comision" value="{{isset($dataComision->id_comisiones) ? $dataComision->id_comisiones : ''}}">
    <input type="hidden" class="form-control" id="fc" name="fc" value="{{isset($dataComision->fecha_nomina) ? $dataComision->fecha_nomina:  date('Y-m-d')}}">
    <input type="hidden" class="form-control" id="c" name="c" value="{{isset($dataComision->cantidad) ? $dataComision->cantidad : 1}}">
    <input type="hidden" class="form-control" id="d" name="d" value="{{isset($dataComision->descripcion) ? $dataComision->descripcion : ''}}">
    <input type="hidden" class="form-control" id="e" name="e" value="{{isset($dataComision->id_empleado) ? $dataComision->id_empleado : ''}}">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <div class="col-md-6" >
                <h3 class="box-title">Ingrese las comisiones a asignar</h3>
            </div>
            @if(empty($dataComision->id_comisiones))
            <div class="col-md-6 text-right" >
                <button type="button" class="btn btn-success" data-toggle="tooltip"
                        title="Agregar comsión" id="btn_add_inputs" onclick="add_inputs()">
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
            <div  id="inputs_comsiones"></div>
            <div class="text-rigth" style="padding-top: 10px">
                <button type="button" class="btn btn-info pull-right" id="btn_store_comsiones" onclick="store_comsiones()">
                    <i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i> Guardar</button>
            </div>
        </div>
    </div>
</form>
<script>
    add_inputs();
</script>
