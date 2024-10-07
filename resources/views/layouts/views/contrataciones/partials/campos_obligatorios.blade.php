<div class="col-md-4" >
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"><i class="fa fa-user-circle-o" aria-hidden="true"></i> Cargo </span>
            <select class="form-control" id="id_cargo" name="id_cargo" onchange="valida_sueldo_sectorial()" required>
                <option disabled selected>Seleccione</option>
                @foreach($dataCargos as $cargo)
                    <option value="{{$cargo->id_cargo}}">{{$cargo->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="row" style="padding: 0px 15px" id="fecha_cargo">
    <div class="col-md-{{isset($dataTipoContrato->caducidad) && $dataTipoContrato->caducidad ? '3' : '4'}}">
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"  style="background: #d9d9d9;"> <i class="fa fa-calendar"></i> Fecha</span>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}"  required style="border-radius: 0px">
            </div>
        </div>
    </div>
    <div class="col-md-{{isset($dataTipoContrato->caducidad) && $dataTipoContrato->caducidad ? '3' : '4'}}">
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"  style="background: #d9d9d9;"> <i class="fa fa-clock-o"></i> Horas laborales</span>
                <input type="number"  class="form-control" id="horas" name="horas" min="1" onkeypress="return filterFloat(event,this)" required style="border-radius: 0px">
            </div>
        </div>
    </div>
    <div class="col-md-{{(isset($dataTipoContrato->caducidad) && $dataTipoContrato->caducidad) ? '3' : '4'}}" id="div_salario">
        <div class="form-group">
            <div class="input-group salario">
                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> <i class="fa fa-money" aria-hidden="true"></i> Salario $</span>
                <input type="number" min="1" class="form-control" id="salario" name="salario" onkeyup="converitr_letras('1',this.value)"
                       placeholder="Ej: 400.00" value="1"  required>
            </div>
        </div>
    </div>
    @if(isset($dataTipoContrato->caducidad) && $dataTipoContrato->caducidad)
        <div class="col-md-3">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"  style="background: #d9d9d9;"> <i class="fa fa-calendar-check-o" aria-hidden="true"></i> Duración (dias)</span>
                    <input type="number" class="form-control" id="cant_dias" name="cant_dias" min="1" onkeypress="return filterFloat(event,this)" required style="border-radius: 0px">
                </div>
            </div>
        </div>
    @endif
</div>
<div class="row sin_relacion_dependencia" style="padding: 0px 15px">
    <div class="col-md-12" id="div_salario_letras">
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                    <i class="fa fa-money" aria-hidden="true"></i> Salario en letras
                </span>
                <input type="text"  class="form-control" id="letras" name="letras" readonly="true"
                       placeholder="Ej: 400.00"  onkeypress="return filterFloat(event,this)" required>
            </div>
        </div>
    </div>

</div>
<script>
    converitr_letras();

    function converitr_letras(sueldo_minimo,input_value) {

        if(sueldo_minimo > 1){
            if(input_value < sueldo_minimo){
                $(".salario label#salario-error").remove();
                $(".salario").append('<label id="salario-error" class="error" for="salario">El salario debe ser mínimo de '+sueldo_minimo+' Dolares.</label>');
                //$("#salario").val(sueldo_minimo);

            }else{
                $(".salario label#salario-error").remove();
            }
        }

        if($("#salario").val().length == 0 )
            $("#letras").val("");

        if($("#salario").val().length > 0){
            setTimeout(function () {
                $.ajax({
                    method : 'GET',
                    url    : '{{url('numero-letras')}}',
                    data   :{
                        cadena : $("#salario").val()
                    },
                    success: function (response) {
                        $("#letras").val(response.trim());
                    }
                });
            },200)
        }
    }

</script>
