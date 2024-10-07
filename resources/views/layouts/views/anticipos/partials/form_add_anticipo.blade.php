@if(empty($dataAnticipo->id_anticipo) && !$mesesContrato)
    <div class="alert alert-danger" role="alert">
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span class="sr-only">Error:</span>
            Debe poseer mínimo {{$antiguedad}} meses en la empresa para poder solicitar anticipos
    </div>
@elseif(empty($dataAnticipo->id_anticipo) && $fechaPlazo)
    <div class="alert alert-danger" role="alert">
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span class="sr-only">Error:</span>
            Los anticipos solo se pueden solicitar hasta el día {{$diaHasta}} de cada mes
    </div>
@elseif(empty($dataAnticipo->id_anticipo) && $intervaloSolicitud)
    <div class="alert alert-danger" role="alert">
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span class="sr-only">Error:</span>
            Los anticipos solo pueden solicitarse cada {{$intervalo}} meses y su último anticipo lo realizó en fecha {{$ultimoAnticipo->fecha_descuento}}
    </div>
@else
    <form id="form_anticipo" name="form_anticipo">
        <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
            <input type="hidden" id="id_anticipo" value="{{!empty($dataAnticipo->id_anticipo) ? $dataAnticipo->id_anticipo : ''}}">
            <div class="box-header with-border">
                <h3 class="box-title">{{in_array('EMPLOYEE',session('dataUsuario')['user_type']) ? 'Ingrese un nueva solicitud de anticipo' : 'Editar solicitud de anticipo'}}</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group" id="cantidad_div">
                            <span class="input-group-addon" style="background: #D9D9D9;">Cantidad</span>
                            <input type="number" min="1" name="cantidad" id="cantidad" class="form-control"
                                    {{in_array('EMPLOYEE',session('dataUsuario')['user_type']) ? '' : 'disabled'}}  onkeyup="verificar_cantidad()"
                                    required value="{{!empty($dataAnticipo->cantidad) ? $dataAnticipo->cantidad : ''}}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group" id="fecha_entrega_div">
                            <span class="input-group-addon" style="background: #D9D9D9;" >F. entrega</span>
                            <input type="date" name="fecha_entrega" onchange="verificar_fecha(this.id)" id="fecha_entrega"
                                    value="{{!empty($dataAnticipo->fecha_entrega) ? $dataAnticipo->fecha_entrega : ''}}" class="form-control" required >
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group" id="fecha_descuento_div">
                            <span class="input-group-addon" style="background: #D9D9D9;" >F. descuento</span>
                            <input type="date" name="fecha_descuento" onchange="verificar_fecha(this.id)"
                                    value="{{!empty($dataAnticipo->fecha_descuento) ? $dataAnticipo->fecha_descuento : ''}}" id="fecha_descuento" class="form-control" required >
                        </div>
                    </div>
                </div>
                <div class="row" style="padding: 20px 0px;">

                    <div class="col-md-12">
                        {{in_array('EMPLOYEE',session('dataUsuario')['user_type']) ? 'El avance del salario no puede ser mayor a (la variable de $ de avance %)' : ''}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" id="btn_store_anticipo" class="btn btn-info pull-right" onclick="store_anticipo()">
                            <i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i>
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endif

