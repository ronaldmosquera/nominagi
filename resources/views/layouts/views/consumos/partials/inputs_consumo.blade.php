<div class="row" id="row_{{$cant_input+1}}" style="margin-bottom: 10px">
<div class="col-md-4">
    <div class="input-group">
        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
            Cantidad
        </span>
        <input type="number" onkeyup="calcular_total(this.id)" onclick="calcular_total(this.id)" min="1" class="form-control" id="cantidad_{{$cant_input+1}}"
               value="1" name="cantidad_{{$cant_input+1}}" required>
    </div>
</div>
<div class="col-md-8">
    <div class="input-group">
        <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
            Producto
        </span>
       <select class="form-control" id="id_producto_{{$cant_input+1}}" name="id_producto_{{$cant_input+1}}"
               onchange="calcular_total(this.id)" required>
           <option disabled selected>Seleccione</option>
           @foreach($dataProductos as $productos)
               <option value="{{$productos->id_productos}}">{{$productos->nombre}}</option>
           @endforeach
       </select>
       <input type="hidden" id="costo_producto_{{$cant_input+1}}" name="costo_producto_{{$cant_input+1}}" value="">
    </div>
</div>
</div>