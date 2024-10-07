<form id="form_cash_management_decimo" name="form_cash_management_decimo">
    <div class="box box-info" style="box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <h3 class="box-title">Gestión de pago {{$tipo  == 'DECIMO_4TO' ? 'decimo 4to sueldo': 'decimo 3er sueldo'}}</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;">
                            Referencia bancaria
                        </span>
                        <input class="form-control" name="referencia_bancaria" id="referencia_bancaria" required>
                    </div>
                </div>
            </div>
            <div style="padding: 25px 0 0 0" class="col-md-12">
                <table class='table table-bordered table-hover'>
                    <thead>
                        <tr>
                            <th style="vertical-align: middle"> EMPLEADO </th>
                            <th style="vertical-align: middle" class="text-center"> FECHA INGRESO </th>
                            <th style="vertical-align: middle" class="text-center"> PERIODO </th>
                            @if($tipo == 'DECIMO_3ER')
                                <th style="vertical-align: middle" class="text-center"> INGRESOS PERIODO </th>
                            @endif
                            <th style="vertical-align: middle" class="text-center"> A PAGAR </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contrataciones as $contratacion)
                            @php
                                $persona = getPerson($contratacion->id_empleado);
                                if($tipo == 'DECIMO_4TO'){
                                    $total = getDecimoCuartoAnual($contratacion->id_empleado);
                                }else{
                                    $total = getDecimoTerceroAnual($contratacion->id_empleado,false,false);
                                }
                            @endphp
                            <tr>
                                <td style="vertical-align: middle">{{$persona->first_name.' '.$persona->last_name}}</td>
                                <td style="vertical-align: middle" class="text-center">{{$contratacion->fecha_expedicion_contrato}}</td>
                                <td style="vertical-align: middle" class="text-center">
                                    @if($tipo == 'DECIMO_4TO')
                                        {{Carbon\Carbon::now()->subYear(1)->format('01-08-Y')}} <b>/</b> {{Carbon\Carbon::now()->format('31-07-Y')}}
                                    @elseif($tipo == 'DECIMO_3ER')
                                        {{Carbon\Carbon::now()->subYear(1)->format('01-12-Y')}} <b>/</b> {{Carbon\Carbon::now()->format('30-11-Y')}}
                                    @endif
                                </td>
                                @if($tipo == 'DECIMO_3ER')
                                    <td style="vertical-align: middle" class="text-center">${{$total}}</td>
                                @endif
                                <td style="vertical-align: middle" class="text-center">${{$total}}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding-top: 10px" class="col-md-12 text-center">
                <button type="button" class="btn btn-info"
                    id="btn_nivelar_nomina" onclick="store_referencia_bancaria_decimo('{{$tipo}}')">
                    <i id="ico" class="fa fa-floppy-o"></i>
                    Guardar referencia
                </button>
                <button type="button" class="btn btn-success"
                    id="btn_nivelar_nomina" onclick="download_cash_management_decimo('{{$tipo}}')">
                    <i id="ico" class="fa fa-cloud-download"></i>
                    Descargar archivo cash
                </button>
            </div>
        </div>
    </div>
</form>
