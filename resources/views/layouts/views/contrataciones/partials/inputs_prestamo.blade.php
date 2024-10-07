
<div class="row container-fluid inputs" id="inputs_{{$cant+1}}">
    <div class="col-md-12"> Prestamo N# {{$cant+1}}</div>
    <div class="row container-fluid">
        <div class="col-md-6" style="margin: 0px 0px 20px;">
            <div class="input-group">
                <span class="input-group-addon" style="background: #D9D9D9;">Nombre prestamo</span>
                <input type="text" id="nombre_prestamo_{{$cant+1}}" name="nombre_prestamo_{{$cant+1}}" class="form-control" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-addon" style="background: #D9D9D9;">Fecha inicio pago</span>
                <input type="date" id="fecha_incio_descuento_{{$cant+1}}" name="fecha_incio_descuento_{{$cant+1}}" class="form-control"
                       onkeypress="" required>
            </div>
        </div>
    </div>
    <div class="row container-fluid">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-addon" style="background: #D9D9D9;">Cuota por nómina $</span>
                <input type="number" min="0" id="cuota_prestamo_{{$cant+1}}" name="cuota_prestamo_{{$cant+1}}" class="form-control" onkeypress="" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-addon" style="background: #D9D9D9;">Total prestamo $</span>
                <input type="number" min="0" id="total_prestamo_{{$cant+1}}" name="total_prestamo_{{$cant+1}}" class="form-control"
                        required>
                <span class="input-group-btn">
                    <button type="button" class="btn btn-danger" data-toggle="tooltip" onclick="delete_inputs_prestamo('inputs_{{$cant+1}}')"
                            title="Eiminar prestamo">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>
    <input type="hidden" class="form-control" id="id_prestamo_{{$cant+1}}" name="id_prestamo_{{$cant+1}}" value="">
    <hr/>
</div>
