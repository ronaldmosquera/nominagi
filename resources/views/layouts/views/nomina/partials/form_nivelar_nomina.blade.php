<form id="form_nivelar_nomina" name="form_nivelar_nomina">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <h3 class="box-title">Ingrese los datos correspondientes</h3>
        </div>
        <div class="box-body">
            <div class="row">
                @foreach($dataContrataciones as $key => $contratacion)
                    @if($contratacion->relacion_dependencia)
                        @php $empleado = getNombreEmpleado($contratacion->id_contrataciones);@endphp
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label>{{$empleado->first_name." ".$empleado->last_name}}</label>
                                <div class="input-group">
                                    <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                                        Últimas vacaciones <i class="fa fa-question-circle" title="Si el empleado no ha gozado nunca de vacaciones colocar la fecha de inicio de la contratación" aria-hidden="true"></i>
                                    </span>
                                    <input type="date" class="form-control" name="fecha_ultimas_vacaciones"
                                           id="{{$contratacion->id_contrataciones}}" value="{{$contratacion->fecha_expedicion_contrato}}" required>
                                </div>
                            </div>
                            @if(!$contratacion->decimo_tercero)
                            <div class="col-md-6">
                                <label>{{$empleado->first_name." ".$empleado->last_name}}</label>
                                <div class="input-group">
                                    <span class="input-group-addon" style="background: #D9D9D9;cursor: pointer" >
                                        Décimo 3ero <i class="fa fa-question-circle" title="Colocar el monto generado por decimos tercero desde Enero hasta Agosto del año anterior" aria-hidden="true"></i>
                                    </span>
                                    <input type="number" min="0" class="form-control" name="decimo_tercero"
                                           id="{{$contratacion->id_contrataciones}}" required>
                                </div>
                            </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
            <div  style="padding-top: 10px" class="col-md-12">
                <button type="button" class="btn btn-info pull-right" id="btn_nivelar_nomina" onclick="nivelar_nomina()">
                    <i id="ico" class="fa fa-floppy-o"></i>
                    Guardar</button>
            </div>
        </div>
    </div>
</form>