<div class="row" id="row_{{$cant_input+1}}" style="margin-bottom: 10px">
    <div class="col-md-12">
        <h4>
            Descuento N° {{$cant_input+1}}
        </h4>
    </div>
    <div class="col-md-12" style="margin-bottom:20px">
        <div class="input-group">
        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
            Empleado
        </span>
            <select class="form-control" id="id_empleado_{{$cant_input+1}}" name="id_empleado_{{$cant_input+1}}"
                    onchange="set_concepto_descuento('id_empleado_{{$cant_input+1}}','id_concepto_{{$cant_input+1}}')" required>
                <option disabled selected>Seleccione</option>
                @foreach($dataEmpleados as $empleados)
                    <option value="{{$empleados->party_id}}">{{$empleados->first_name}} {{$empleados->last_name}}</option>
                @endforeach
            </select>
            <span class="input-group-btn" id='btn_delete_descuento_row'>
                <button type="button" class="btn btn-danger" data-toggle="tooltip" onclick="delete_inputs('row_{{$cant_input+1}}')"
                    title="Eiminar comsión">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
            </span>
        </div>
    </div>
    <div class="col-md-6" >
        <div class="input-group">
            <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                Concepto
            </span>
            <select class="form-control" id="id_concepto_{{$cant_input+1}}"
                    onchange="set_nombre_descuento('{{$cant_input+1}}')" name="id_concepto_{{$cant_input+1}}"
                    id="id_concepto_{{$cant_input+1}}" required>
                    <option disabled selected>Seleccione</option>
            </select>
        </div>
    </div>
    <div class="col-md-6"  style="margin-bottom: 20px">
        <div class="input-group">
            <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                Fecha nómina
            </span>
            <input type="date" class="form-control" id="fecha_descuento_{{$cant_input+1}}"
                   name="fecha_descuento_{{$cant_input+1}}" required>
        </div>
    </div>
    <div class="col-md-6" style="margin-bottom: 20px">
        <div class="input-group">
            <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                Nombre
            </span>
            <input type="text" class="form-control" id="nombre_{{$cant_input+1}}"
                   name="nombre_{{$cant_input+1}}" required>

        </div>
        </div>
    <div class="col-md-6">
        <div class="input-group">
        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
            Monto $
        </span>
            <input type="number" min="0"  class="form-control" id="cantidad_{{$cant_input+1}}"
                    value="1" name="cantidad_{{$cant_input+1}}" required>
        </div>

    </div>
    <div class="col-md-12 text-area" style="margin-top: 20px">
        <textarea class="form-control" id="descripcion_{{$cant_input+1}}"
                    required placeholder="Motivo de decuentos" name="descripcion_{{$cant_input+1}}"></textarea>
    </div>
</div>

