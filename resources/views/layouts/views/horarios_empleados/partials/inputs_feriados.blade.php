@isset($cant_input)
    <div class="col-md-4">
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon" style="background: #d9d9d9;"> <i class="fa fa-calendar-plus-o" ></i> Feriado </span>
                <input type="date" class="form-control fecha_feriado" id="feriado_{{$cant_input+1}}" name="feriado_{{$cant_input+1}}"
                       required style="border-radius: 0px">
            </div>
        </div>
    </div>
@endisset
@isset($fechas_feriado)
    @foreach($fechas_feriado as $x => $ff)
        <div class="col-md-4">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon" style="background: #d9d9d9;"> <i class="fa fa-calendar-plus-o" ></i> Feriado </span>
                    <input type="date" class="form-control fecha_feriado" id="feriado_{{$x+1}}" name="feriado_{{$x+1}}"
                           value="{{$ff->fecha_feriado}}" required style="border-radius: 0px">
                </div>
            </div>
        </div>
    @endforeach
@endisset
