<form id="form_cash_management_prestamos" name="form_cash_management_prestamos">
    <div class="box box-info" style="box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <h3 class="box-title">Gestión de pago de prestamos</h3>
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
                            <th style="vertical-align: middle" class="text-center"> PAGAR </th>
                            <th style="vertical-align: middle"> EMPLEADO </th>
                            <th style="vertical-align: middle" class="text-center"> TOTAL </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($prestamos as $prestamo)
                            <tr>
                                <td style="vertical-align: middle" class="text-center">
                                    <input type="checkbox" id="{{$prestamo->id_contratacion}}" class="check_pago_prestamo" >
                                </td>
                                <td style="vertical-align: middle" >{{$prestamo->persona}}</td>
                                <td style="vertical-align: middle" class="text-center">${{$prestamo->total}}</td>
                            </tr>
                        @empty
                            <td colspan="3">
                                <div class="alert alert-info text-center">Sin Prestamos realizados</div>
                            </td>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding-top: 10px" class="col-md-12 text-center">
                <button type="button" class="btn btn-info"
                    id="btn_nivelar_nomina" onclick="store_referencia_bancaria_prestamo()">
                    <i id="ico" class="fa fa-floppy-o"></i>
                    Guardar referencia
                </button>
                <button type="button" class="btn btn-success"
                    id="btn_nivelar_nomina" onclick="download_cash_management_prestamos()">
                    <i id="ico" class="fa fa-cloud-download"></i>
                    Descargar archivo cash
                </button>
            </div>
        </div>
    </div>
</form>
