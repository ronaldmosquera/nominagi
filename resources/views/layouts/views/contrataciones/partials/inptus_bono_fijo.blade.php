<div class="row container-fluid" id="inputs">
    <div class="col-md-12"> Prestamo N# {{$cant+1}}</div>
    <div class="col-md-4" style="margin: 0px 0px 20px;"
            title="Si se escoge 'SI' se afectara el aporte personal, aporte patronal y el decimo 3ero en los contratos bajo relación de dependecia">
        <div class="input-group">
            <span class="input-group-addon" style="background: #D9D9D9;">Afecta aporte personal</span>
            <select class="form-control" name='apt_patronal_{{$cant+1}}' id="apt_patronal_{{$cant+1}}">
                <option value="0">No</option>
                <option value="1">Si</option>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-addon" style="background: #D9D9D9;">Fecha</span>
            <input type="date" id="fecha_asignacion_{{$cant+1}}" name="fecha_asignacion_{{$cant+1}}" class="form-control"
                    required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-addon" style="background: #D9D9D9;">Monto</span>
            <input type="number" min="1" id="monto_bono_fijo_{{$cant+1}}" name="monto_bono_fijo_{{$cant+1}}" class="form-control"
                    required>
        </div>
    </div>
    <div class="col-md-12" >
        <div class="input-group">
            <span class="input-group-addon" style="background: #D9D9D9;">Nombre</span>
            <input type="text" id="nombre_bono_fijo_{{$cant+1}}" name="nombre_bono_fijo_{{$cant+1}}" class="form-control" required>
        </div>
    </div>
    <input type="hidden" class="form-control" id="id_bono_fijo_{{$cant+1}}" name="id_bono_fijo_{{$cant+1}}" value="">
</div>
<hr/>
