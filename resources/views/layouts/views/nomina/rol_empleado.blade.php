@extends('layouts.principal')
@section('title')
    Rol de pago
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header" >
                    <div class="col-md-12 text-center">
                        <h3 class="box-title">
                            Vista previa de rol de pago correspondiente al empleado {{(isset($dataVistaNomina['nombre_empleado']) && $dataVistaNomina['nombre_empleado']) ? $dataVistaNomina['nombre_empleado'] : ""}}
                        </h3>
                    </div>
                    @if(isset($var))
                        <section class="content">
                            <div class="" style="margin-top: 40px">
                                <div class="alert alert-info col-md-12" role="alert">
                                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                    <span class="sr-only">Error:</span>
                                    {!! $message !!}
                                </div>

                            </div>
                        </section>
                    @else
                        @php
                            $tipoContrato = \App\Models\Contrataciones::where('id_contrataciones',$dataVistaNomina['id_contratacion'])
                            ->join('tipo_contrato as tp','tp.id_tipo_contrato','contrataciones.id_tipo_contrato')->select('relacion_dependencia')->first();
                        @endphp
                        <div class="" style="margin-bottom: 50px">
                            <section class="content">
                                <div class="row" style="margin-top: 40px">
                                    <div class="col-md-8 col-md-offset-2" style="border: 1px solid #555555;margin-bottom: 35px;border-radius: 5px">
                                        <div style="background: #c3d69b;padding: 3px;font-weight: 600; margin: 15px 0px;" class="text-center">
                                            @if($tipoContrato->relacion_dependencia)
                                                {{"ROL DE PAGOS"}}
                                            @else
                                                {{"PAGO DE SERIVICIOS"}}
                                            @endif
                                        </div>
                                        <table style="width: 100%;">
                                            <tr >
                                                <td style="text-align: left;font-size: 11pt;font-weight: 600">Nombre</td>
                                                <td style="text-align: right;font-size: 11pt;font-weight: 600">{{$dataVistaNomina['nombre_empleado']}}</td>
                                            </tr>
                                            <tr >
                                                <td style="text-align: left;font-size: 11pt;font-weight: 600">Cargo</td>
                                                <td style="text-align: right;font-size: 11pt;font-weight: 600">{{$dataVistaNomina['cargo']}}</td>
                                            </tr>
                                            <tr >
                                                <td style="text-align: left;font-size: 11pt;font-weight: 600">{{ucwords($dataVistaNomina['documento'])}}</td>
                                                <td style="text-align: right;font-size: 11pt;font-weight: 600">{{$dataVistaNomina['identificacion']}}</td>
                                            </tr>
                                            <tr >
                                                <td style="text-align: left;font-size: 11pt;font-weight: 600">Nómina</td>
                                                <td style="text-align: right;font-size: 11pt;font-weight: 600">
                                                    {{getMes(intval(\Carbon\Carbon::now()->subMonth(1)->format('m')))}} del {{\Carbon\Carbon::now()->format('m') == 01
                                                    ? \Carbon\Carbon::now()->subYear(1)->format('Y')
                                                    : \Carbon\Carbon::now()->format('Y')}}
                                                </td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%;margin: 20px 0px">
                                            <thead>
                                                <tr class="text-center">
                                                    <td style="font-weight: 900;font-size: 11pt;border: 1px solid black">
                                                        INGRESOS
                                                    </td>
                                                    <td style="font-weight: 900;font-size: 11pt;border: 1px solid black">
                                                        EGRESOS
                                                    </td>
                                                </tr>
                                            </thead>
                                                <tbody style="border: 1px solid black">
                                                    <tr>
                                                        <td>
                                                            <table style="width: 100.5%;">
                                                                <thead>
                                                                <tr>
                                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                        @if($tipoContrato->relacion_dependencia)
                                                                            Salario
                                                                        @else
                                                                            Base
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                        {{"$".$dataVistaNomina['salario']}}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                        @if($tipoContrato->relacion_dependencia)
                                                                            Horas extras
                                                                        @else
                                                                            TRAX Adicionales
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                        {{"$".$dataVistaNomina['horas_extras']}}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                        Comisiones
                                                                    </td>
                                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                        {{"$".$dataVistaNomina['comisiones']}}
                                                                    </td>
                                                                </tr>
                                                                @foreach($dataVistaNomina['arr_bonos_fijos'] as $bonosFijos)
                                                                <tr>
                                                                     <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                         {{ucfirst($bonosFijos->nombre)}}
                                                                     </td>
                                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                        {{"$".number_format($bonosFijos->monto,2,".","")}}
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                                @if($dataVistaNomina['iva'] != false)
                                                                    <tr>
                                                                        <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                            Iva
                                                                        </td>
                                                                        <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                            {{"$".number_format($dataVistaNomina['iva'],2,".","")}}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                @if($tipoContrato->relacion_dependencia)
                                                                    <tr>
                                                                        <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                            Decimo tercero
                                                                        </td>
                                                                        <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                            {{$dataVistaNomina['decimo_tercero'] == "N/A" ? $dataVistaNomina['decimo_tercero'] : "$".$dataVistaNomina['decimo_tercero']}}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                            Decimo cuarto
                                                                        </td>
                                                                        <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                            {{$dataVistaNomina['decimo_cuarto'] == "N/A" ? $dataVistaNomina['decimo_cuarto'] : "$".$dataVistaNomina['decimo_cuarto']}}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                            Fondo reserva
                                                                        </td>
                                                                        <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                            {{$dataVistaNomina['fondo_reserva'] == "N/A" ? $dataVistaNomina['fondo_reserva'] : "$".$dataVistaNomina['fondo_reserva']}}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                <tr>
                                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                        Total Ingresos
                                                                    </td>
                                                                    <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;border-top: 1px solid black">
                                                                        {{"$".number_format($dataVistaNomina['ingresos'],2,".","") }}
                                                                    </td>
                                                                </tr>
                                                                </thead>
                                                            </table>
                                                        </td>
                                                        <td>
                                                            <table style="width: 100%">
                                                                @if($tipoContrato->relacion_dependencia)
                                                                    <tr >
                                                                        <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                            Aporte personal al IESS
                                                                        </td>
                                                                        <td class="text-center">
                                                                            {{$dataVistaNomina['aporte_personal_IESS'] == "N/A" ? $dataVistaNomina['aporte_personal_IESS'] : "$".$dataVistaNomina['aporte_personal_IESS']}}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                <tr >
                                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                        Consumos
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{"$".$dataVistaNomina['consumos']}}
                                                                    </td>
                                                                </tr>
                                                                @foreach($dataVistaNomina['arrPrestamos'] as $prestamos)
                                                                    <tr>
                                                                        <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                            {{ucfirst($prestamos->nombre)}}
                                                                        </td>
                                                                        <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                            {{"$".number_format($prestamos->cuota,2,".","")}}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                @if($dataVistaNomina['retencionIva'] != false)
                                                                    <tr>
                                                                        <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                            Retención iva
                                                                        </td>
                                                                        <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                            {{"$".number_format($dataVistaNomina['retencionIva'],2,".","")}}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                @if($dataVistaNomina['retencionRenta'] != false)
                                                                    <tr>
                                                                        <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                            Retención renta
                                                                        </td>
                                                                        <td class="text-center" style="font-size: 11pt;border-right: 1px solid black;padding:0px 5px;">
                                                                            {{"$".number_format($dataVistaNomina['retencionRenta'],2,".","")}}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                <tr>
                                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                        Anticipos
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{"$".$dataVistaNomina['anticipos']}}
                                                                    </td>

                                                                </tr>
                                                                @if(count($dataVistaNomina['otros_descuentos'])>0)
                                                                    <tr >
                                                                        <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                            Otros descuentos
                                                                            <ul style="padding-left: 5px;margin: 0;">
                                                                                @foreach($dataVistaNomina['otros_descuentos'] as $descuentoNombre)
                                                                                    <li style="list-style: none">
                                                                                        <table style="width: 100%;">
                                                                                            <tr>
                                                                                                <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                                                    {{$descuentoNombre->nombre}}
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <table style="width: 100%;">
                                                                                <tr>
                                                                                    <td>
                                                                                        <ul style="margin: 0;padding: 0;">
                                                                                            @foreach($dataVistaNomina['otros_descuentos'] as $key => $descuentoMonto)
                                                                                                <li style="list-style: none;">
                                                                                                    <table style="width: 100%;">
                                                                                                        <tr>
                                                                                                            <td {{$key == 0 ? "style=padding-top:17px" : ""}} >
                                                                                                                {{"$".number_format($descuentoMonto->cantidad,2,".","")}}
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    </table>
                                                                                                </li>
                                                                                            @endforeach
                                                                                        </ul>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                <tr >
                                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;">
                                                                        Total Egresos
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{"$".number_format($dataVistaNomina['egresos'],2,".","")}}
                                                                    </td>
                                                                </tr>
                                                                <tr >
                                                                    <td style="font-weight: 900;font-size: 11pt;padding:0px 5px;border: 1px solid black">
                                                                        Total a recibir
                                                                    </td>
                                                                    <td class="text-center" style="font-weight: 900;font-size: 12pt;border: 1px solid black">
                                                                        {{"$".$dataVistaNomina['total']}}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                        </table>
                                        <div class="text-center" style="margin-top: 60px">
                                            <P>___________________________</P>
                                            <p style="font-size: 11pt;font-weight: 800"> FIRMA</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection