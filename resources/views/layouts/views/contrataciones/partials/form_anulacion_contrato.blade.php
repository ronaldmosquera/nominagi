<form id="form_anulacion_contrato" novalidate="novalidate">
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <h3 class="box-title">Seleccione el motivo de terminación del contrato</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                            <i class="fa fa-user-times" aria-hidden="true"></i>
                            Motivo terminación
                        </span>
                        <select class="form-control" id="id_motivo_anulacion" required>
                            <option disabled selected> Seleccione </option>
                            @foreach($dataMotivoAnulacion as $motivoAnulacion)
                                <option value="{{$motivoAnulacion->id_motivo_anulacion}}"> {{$motivoAnulacion->nombre}} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;">
                            <i class="fa fa-calendar"></i>
                            Fecha de terminación
                        </span>
                        <input type="date" class="form-control" id="fecha_terminacion" name="fecha_terminacion" required>
                    </div>
                </div>
            </div>

                <div class="row" style="margin-top: 30px">
                    <div class="col-md-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-cubes"></i> Selecione con un check la opciones que aplican</h3>
                            </div>
                            <div class="panel-body">
                                @if($relacion_dependencia)
                                    <div class="col-md-3">
                                        <input type="checkbox" id="despido_visto_bueno" name="despido_visto_bueno">
                                        <label for="despido_visto_bueno"> Despido por visto bueno</label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="checkbox" id="bono_25_porciento" name="bono_25_porciento">
                                        <label for="bono_25_porciento"> Bonificación 25%</label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="checkbox" id="indemnizacion_terminación_antes_plazo" name="indemnizacion_terminación_antes_plazo">
                                        <label for="indemnizacion_terminación_antes_plazo"> Indemnización por terminación antes del plazo</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="checkbox" id="indemnizacion_discapacidad" name="indemnizacion_discapacidad">
                                        <label for="indemnizacion_discapacidad"> Indemnización por discpacidad</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="checkbox" id="despido_ineficaz" name="despido_ineficaz">
                                        <label for="despido_ineficaz">Indemnización por despido ineficaz declarado por juez competente por dirigente</label>
                                    </div>
                                @else
                                    <div class="col-md-12">
                                        <input type="checkbox" id="indemnizacion_terminación_antes_plazo" name="indemnizacion_terminación_antes_plazo">
                                        <label for="indemnizacion_terminación_antes_plazo"> Indemnización por terminación antes del plazo</label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            <div class="row text-center">
                <div class="col-md-12" style="padding: 10px 0px">
                    <button type="button" class="btn btn-info" id="btn_store_contrato" onclick="store_terminar_contratatacion('{{$idContrato}}',0)">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                        Vista previa</button>
                </div>
            </div>
        </div>
    </div>
</form>