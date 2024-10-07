<div class="row" id="row_{{$cant_input+1}}" style="margin-bottom: 10px">
    <div class="col-md-12">
        <h4>
            Comisión N° {{$cant_input+1}}
        </h4>
    </div>
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                Fecha nómina
            </span>
            <input type="date" class="form-control" id="fecha_comision_{{$cant_input+1}}"
                   name="fecha_comision_{{$cant_input+1}}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="input-group">
        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
            Empleado
        </span>
            <select class="form-control" id="id_empleado_{{$cant_input+1}}" name="id_empleado_{{$cant_input+1}}" required>
                <option disabled selected>Seleccione</option>
                @foreach($dataEmpleados as $empleados)
                    <option value="{{$empleados->party_id}}">{{$empleados->first_name}} {{$empleados->last_name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6" style="margin-top: 20px">
        <div class="input-group">
        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
            Comisión
        </span>
            <select class="form-control" id="id_tipo_comision_{{$cant_input+1}}" name="{{$cant_input+1}}"
                    onchange="asignar_comision(this)" required>
                <option disabled selected>Seleccione</option>
                @foreach($dataComisiones as $comision)
                    <option value="{{$comision->id_tipo_comision}}">{{$comision->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6" style="margin-top: 20px">
        <div class="input-group">
        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
            Monto $
        </span>
            <input type="number" min="1"  class="form-control" id="cantidad_{{$cant_input+1}}"
                     value="1" name="cantidad_{{$cant_input+1}}" required>
        </div>
    </div>
    <div class="col-md-12" style="margin-top: 20px">
        <textarea class="form-control" id="descripcion_{{$cant_input+1}}"
                  required placeholder="Motivo por el cual se asigna la comisión" name="descripcion"></textarea>
    </div>
</div>
