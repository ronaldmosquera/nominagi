<form id="form_cash_management_decimo" name="form_cash_management_decimo">
    <div class="box box-info" style="box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <h3 class="box-title">Gestión de pagos de alcances de nómina</h3>
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
                            <th style="vertical-align: middle" class="text-center"> SALARIO </th>
                            <th style="vertical-align: middle" class="text-center"> HORAS EXTRAS </th>
                            <th style="vertical-align: middle" class="text-center"> COMISIONES </th>
                            <th style="vertical-align: middle" class="text-center"> BONOS </th>
                            <th style="vertical-align: middle" class="text-center"> DCMO 3ER </th>
                            <th style="vertical-align: middle" class="text-center"> DCMO 4TO </th>
                            <th style="vertical-align: middle" class="text-center"> APT PERSONAL </th>
                            <th style="vertical-align: middle" class="text-center"> FONDO RESERVA </th>
                            <th style="vertical-align: middle" class="text-center"> IVA </th>
                            <th style="vertical-align: middle" class="text-center"> RET. IVA </th>
                            <th style="vertical-align: middle" class="text-center"> RET. RENTA </th>
                            <th style="vertical-align: middle" class="text-center"> TOTAL </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alcancesNominas as $alcancesNomina)
                            <tr>
                                @php
                                    $nomina  = $Nomina::find($alcancesNomina->id_nomina);
                                    $persona = getPerson($nomina->id_empleado);
                                @endphp
                                <td style="vertical-align: middle"> {{$persona->first_name.' '.$persona->last_name}} </td>
                                <td style="vertical-align: middle" class="text-center">${{number_format($alcancesNomina->sueldo,2)}} </td>
                                <td style="vertical-align: middle" class="text-center">${{number_format($alcancesNomina->hora_extra,2)}} </td>
                                <td style="vertical-align: middle" class="text-center">${{number_format($alcancesNomina->comision,2)}} </td>
                                <td style="vertical-align: middle" class="text-center">${{number_format($alcancesNomina->bono,2)}} </td>
                                <td style="vertical-align: middle" class="text-center">${{number_format($alcancesNomina->dcmo_3ro,2)}} </td>
                                <td style="vertical-align: middle" class="text-center">${{number_format($alcancesNomina->dcmo_4to,2)}} </td>
                                <td style="vertical-align: middle" class="text-center">${{number_format($alcancesNomina->aporte_personal,2)}} </td>
                                <td style="vertical-align: middle" class="text-center">${{number_format($alcancesNomina->fondo_reserva,2)}} </td>
                                <td style="vertical-align: middle" class="text-center">${{number_format($alcancesNomina->iva,2)}} </td>
                                <td style="vertical-align: middle" class="text-center">${{number_format($alcancesNomina->retencion_iva,2)}} </td>
                                <td style="vertical-align: middle" class="text-center">${{number_format($alcancesNomina->retencion_renta,2)}} </td>
                                <td style="vertical-align: middle" class="text-center">${{number_format($alcancesNomina->total,2)}} </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding-top: 10px" class="col-md-12 text-center">
                <button type="button" class="btn btn-info"
                    id="btn_nivelar_nomina" onclick="store_referencia_bancaria_alcances_nomina()">
                    <i id="ico" class="fa fa-floppy-o"></i>
                    Guardar referencia
                </button>
                <button type="button" class="btn btn-success"
                    id="btn_nivelar_nomina" onclick="download_cash_management_alcances_nomina()">
                    <i id="ico" class="fa fa-cloud-download"></i>
                    Descargar archivo cash
                </button>
            </div>
        </div>
    </div>
</form>
