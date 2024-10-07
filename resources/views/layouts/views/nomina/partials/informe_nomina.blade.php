@extends('layouts.principal')
@section('title')
    Informe de nómina
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-header">
                        <table width="100%">
                            <tr>
                                <td style="vertical-align: middle;width:33.33%;">
                                    <h4>
                                        <i class="fa fa-table"></i>
                                        informe de nómina
                                    </h4>
                                </td>
                                <td style="vertical-align: middle;width:33.33%;">
                                    <div class="input-group">
                                        <span class="input-group-addon">Seleccione una fecha</span>
                                        <input type="date" id="fecha_nomina" name="fecha_nomina" class="form-control">
                                        <span class="input-group-btn">
                                        <button class="btn btn-primary" type="button" onclick="generar_informe_nomina()">
                                            <i class="fa fa-cog" ></i> Generar
                                        </button>
                                    </span>
                                    </div>
                                </td>
                                <td class="text-right" style="vertical-align: middle;width:33.33%;"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <div class="col-md-12 informe_nomina"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('custom_page_js')
    @include('layouts.views.nomina.script')
@endsection