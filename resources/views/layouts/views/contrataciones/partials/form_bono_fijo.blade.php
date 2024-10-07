<div class="">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#bonos" data-toggle="tab" aria-expanded="true">Bonos fijos</a></li>
            <li class=""><a href="#prestamos" data-toggle="tab" aria-expanded="true">Prestamos</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="bonos">
                <!-- Post -->
                <form id="form_bono_fijo" name="form_bono_fijo">
                    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
                        <div class="box-header with-border">
                            <div class="col-md-8">
                                <h3 class="box-title">
                                    <i class="fa fa-money" aria-hidden="true"></i>
                                    Ingrese el bono fijo a asignar
                                </h3>
                            </div>
                            <div class="col-md-4 text-right">
                                <button type="button" class="btn btn-success" data-toggle="tooltip"
                                        title="Agregar bono fijo" id="btn_add_inputs" onclick="add_inputs_bono_fijo()">
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="tooltip" onclick="delete_inputs_bono_fijo()"
                                        title="Eiminar bono fijo">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div id="inputs_bono_fijo">
                                @if(isset($dataBono) && $dataBono->count() > 0)
                                    @foreach($dataBono->get() as $key => $bono)
                                        <div class="col-md-12"> Bono N# {{$key+1}}</div>
                                        <div class="row container-fluid" id="inputs">
                                            <div class="col-md-4" style="margin: 0px 0px 20px;"
                                                    title="Si se escoge 'SI' se afectara el aporte personal, aporte patronal y el decimo 3ero en los contratos bajo relación de dependecia">
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="background: #D9D9D9;">Afecta aporte personal</span>
                                                    <select class="form-control" name='apt_patronal_{{$key+1}}' id="apt_patronal_{{$key+1}}">
                                                        <option {{!$bono->apt_personal ? 'selected' : ''}} value="0">No</option>
                                                        <option {{$bono->apt_personal ? 'selected' : ''}} value="1">Si</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="background: #D9D9D9;">Fecha</span>
                                                    <input type="date" id="fecha_asignacion_{{$key+1}}" name="fecha_asignacion_{{$key+1}}" class="form-control"
                                                            value="{{$bono->fecha_asignacion}}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="background: #D9D9D9;">Monto</span>
                                                    <input type="number" min="1" id="monto_bono_fijo_{{$key+1}}" name="monto_bono_fijo_{{$key+1}}"
                                                            value="{{$bono->monto}}" class="form-control" required>
                                                    <span class="input-group-btn">
                                                        <button type="button" id="btn_delete_bono_fijo_{{$key+1}}" class="btn btn-danger" onclick="delete_bono_fijo('{{$bono->id_bono_fijo}}')" title="Eliminar Bono fijo">
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-12" style="margin: 0px 0px 20px;">
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="background: #D9D9D9;">Nombre</span>
                                                    <input type="text" id="nombre_bono_fijo_{{$key+1}}" name="nombre_bono_fijo_{{$key+1}}" class="form-control" value="{{$bono->nombre}}" required>
                                                </div>
                                            </div>

                                            <input type="hidden" class="form-control" id="id_bono_fijo_{{$key+1}}" name="id_bono_fijo_{{$key+1}}" value="{{$bono->id_contratacion}}">
                                        </div>
                                        <hr />
                                    @endforeach
                                @endif
                            </div>
                            <div class="col-md-12 text-center" style="padding-top: 10px">
                                <button type="button" class="btn btn-info" id="btn_store_bono_fijo"
                                        onclick="store_bono_fijo('{{$idContratacion}}')">
                                    <i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i> Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <script>@if(isset($dataBono) && $dataBono->count() == 0)add_inputs_bono_fijo() @endif</script>
            </div>
            <div class="tab-pane" id="prestamos">
                <form id="form_prestamos" name="form_prestamos">
                    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
                        <div class="box-header with-border">
                            <div class="col-md-8">
                                <h3 class="box-title">
                                    <i class="fa fa-money" aria-hidden="true"></i>
                                    Ingrese el prestamo a asignar
                                </h3>
                            </div>
                            <div class="col-md-4 text-right">
                                <button type="button" class="btn btn-success" data-toggle="tooltip"
                                        title="Agregar prestamo" id="btn_add_inputs_prestamos" onclick="add_inputs_prestamos()">
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                </button>

                            </div>
                        </div>
                        <div class="box-body">
                            <div id="inputs_prestamo">
                                @if(isset($dataPrestamo) && $dataPrestamo->count() > 0)
                                    @foreach($dataPrestamo->get() as $key => $prestamo)
                                        <div class="row container-fluid inputs" id="inputs_{{$key+1}}">
                                            <div class="row container-fluid">
                                                <div class="col-md-12">Prestamo N# {{$key+1}} <b>{{!$prestamo->pagado ? 'Pagado: $'.$prestamo->abonado : ''}}</b> </div>
                                                <div class="col-md-6" style="margin: 0px 0px 20px;">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" style="background: #D9D9D9;">Nombre prestamo</span>
                                                        <input type="text" id="nombre_prestamo_{{$key+1}}" name="nombre_prestamo_{{$key+1}}"
                                                               value="{{$prestamo->nombre}}" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" style="background: #D9D9D9;">Fecha inicio pago</span>
                                                        <input type="date" id="fecha_incio_descuento_{{$key+1}}" name="fecha_incio_descuento_{{$key+1}}" class="form-control"
                                                               value="{{$prestamo->fecha_inicio_descuento}}"   required>
                                                    </div>
                                                </div>
                                                {{--<div class="col-md-1">
                                                    <button type="button" class="btn btn-warning " title="Eliminar prestamo" onclick="eliminar_prestamo('{{$prestamo->id_prestamo}}')">
                                                        <i class="fa fa-ban"></i>
                                                    </button>
                                                </div>--}}
                                            </div>
                                            <div class="row container-fluid">
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" style="background: #D9D9D9;">Cuota por nómina $</span>
                                                        <input type="number" min="1" id="cuota_prestamo_{{$key+1}}" name="cuota_prestamo_{{$key+1}}" class="form-control"
                                                               value="{{$prestamo->cuota}}"   required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" style="background: #D9D9D9;">Total prestamo $</span>
                                                        <input type="number" min="1" id="total_prestamo_{{$key+1}}" name="total_prestamo_{{$key+1}}" class="form-control"
                                                               value="{{$prestamo->total}}" required>
                                                        <span class="input-group-btn">
                                                            <button type="button" class="btn btn-danger" data-toggle="tooltip" title="Eiminar prestamo"
                                                                    onclick="delete_inputs_prestamo('inputs_{{$key+1}}','{{$prestamo->id_prestamo}}')">
                                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" class="form-control" id="id_prestamo_{{$key+1}}" name="id_prestamo_{{$key+1}}" value="{{$prestamo->id_prestamo}}">
                                            <hr/>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="col-md-12 text-center" style="padding-top: 10px">
                                <button type="button" class="btn btn-default" id="btnclose" data-dismiss="modal">
                                    <i id="ico" class="fa fa-ban" ></i> Cerrar
                                </button>
                                <button type="button" class="btn btn-info" id="btn_store_prestamo" onclick="store_prestamo('{{$idContratacion}}','{{$persona}}')">
                                    <i id="ico" class="fa fa-floppy-o" ></i> Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <script>@if(isset($dataPrestamo) && $dataPrestamo->count() == 0)  add_inputs_prestamos();  @endif</script>
            </div>
        </div>
    </div>
</div>
