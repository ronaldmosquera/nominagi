<form id="form_horas_extras" name="form_horas_extras">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <div class="col-md-6" >
                <h3 class="box-title">Ingrese las horas extras a solicitar</h3>
            </div>
           {{--<div class="col-md-6 text-right" >
             <button type="button" class="btn btn-success" data-toggle="tooltip"
                     title="Agregar hora extra" id="btn_add_inputs" onclick="add_inputs('{{session('dataUsuario')['id_empleado']}}')">
                 <i class="fa fa-plus-circle" aria-hidden="true"></i>
             </button>
                <button type="button" class="btn btn-danger" data-toggle="tooltip" onclick="delete_inputs()"
                        title="Quitar hora extra">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
            </div>--}}
        </div>
        <div class="box-body">
            <div  id="inputs_horas_extras"></div>
            @if(!empty($dataVariables->hora_extra_entre_semana) && !empty($dataVariables->hora_extra_fin_semana))
            <div class="col-md-9" style="padding-top: 10px">
            <em><b>NOTA:</b> Cada hora extra se pagará por la cantidad de ${{$dataVariables->hora_extra_entre_semana}} (Entre semana) y ${{$dataVariables->hora_extra_fin_semana}} (fines de semanas)</em>
            </div>
            @endif
            <div style="padding-top: 10px">
                <button type="button" class="btn btn-info pull-right" id="btn_store_horas_extras" onclick="store_horas_extras({{session('dataUsuario')['id_empleado']}})">
                    <i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i> Guardar</button>
            </div>
        </div>
    </div>
</form>
<script>
    add_inputs('{{session('dataUsuario')['id_empleado']}}','{{$id_horas_extras}}');
</script>