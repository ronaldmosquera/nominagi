<table class="table">
    <tbody>
    <tr>
        <th class="bg-info text-center" colspan="20">Nómina generada al: {{\Carbon\Carbon::parse($fechaNomina)->format('d/m/Y')}}</th>
    </tr>
    <tr>
        <th class="bg-info" colspan="3" style="border:1px solid silver;text-align: center;vertical-align: middle">Datos</th>
        <th class="bg-success" style="border:1px solid silver;text-align: center;vertical-align: middle" colspan="10">
            <i class="fa fa-plus"></i> INGRESOS</th>
        <th class="bg-danger" style="border:1px solid silver;text-align: center;vertical-align: middle" colspan="6">
            <i class="fa fa-minus"></i> EGRESOS
        </th>
        <th style="border:1px solid silver;text-align: center;vertical-align: middle"></th>
    </tr>
    <tr>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center colum_nombre th_nombre">Nombre</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">Identificación</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">Cargo</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">Sueldo</th>
        <th style="border:1px solid silver;width: 50px;vertical-align: middle" class="text-center">H.E 50%</th>
        <th style="border:1px solid silver;width: 50px;vertical-align: middle" class="text-center">H.E 100%</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">Comisones</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">Bonos</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">Iva</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">10mo 3ero</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">10mo 4to</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">Fondo reserva</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">Aporte patronal IESS</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">Anticipos</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">Consumos</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">Prestamos</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">Descuentos</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">Ret. Iva</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">Ret. renta</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center">Aporte personal IESS</th>
        <th style="border:1px solid silver;vertical-align: middle" class="text-center bg-primary">Total</th>
    </tr>
    @if(isset($arrDataInformeNomina) && count($arrDataInformeNomina)>0)
        @foreach($arrDataInformeNomina as $informeNomina)
            <tr style="font-size: 12px">
                <td style="border:1px solid silver;vertical-align: middle" class="text-center colum_nombre nombre">{{ucfirst($informeNomina['empleado'])}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">{{ucfirst($informeNomina['identificacion'])}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">{{ucfirst($informeNomina['cargo'])}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">${{number_format($informeNomina['sueldo'],2,".",",")}}</td>
                <td style="border:1px solid silver;width: 50px;vertical-align: middle" class="text-center">${{number_format($informeNomina['H_E_50'],2,".",",")}}</td>
                <td style="border:1px solid silver;width: 50px;vertical-align: middle" class="text-center">${{number_format($informeNomina['H_E_100'],2,".",",")}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">${{number_format($informeNomina['comsiones'],2,".",",")}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">${{number_format($informeNomina['Bonos'],2,".",",")}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">${{number_format($informeNomina['iva'],2,".",",")}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">${{number_format($informeNomina['10mo_3ero'],2,".",",")}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">${{number_format($informeNomina['10mo_4to'],2,".",",")}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">${{number_format($informeNomina['fondo_reserva'],2,".",",")}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">${{number_format($informeNomina['aporte_patronal'],2,".",",")}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">${{number_format($informeNomina['anticipos'],2,".",",")}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">${{number_format($informeNomina['consumos'],2,".",",")}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">${{number_format($informeNomina['prestamos'],2,".",",")}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">${{number_format($informeNomina['descuento'],2,".",",")}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">${{number_format($informeNomina['retencion_iva'],2,".",",")}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">${{number_format($informeNomina['retencion_renta'],2,".",",")}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center">${{number_format($informeNomina['aporte_personal'],2,".",",")}}</td>
                <td style="border:1px solid silver;vertical-align: middle" class="text-center bg-primary"><b>${{number_format($informeNomina['total'],2,".","")}}</b></td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="21" class="alert alert-warning text-center">No hay nómina aprobada en la fecha seleccionada</td>
        </tr>
    @endif
    </tbody>
</table>