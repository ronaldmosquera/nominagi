@extends('layouts.principal')
@section('title')
    Nómina
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                   <table width="100%">
                       <tr>
                           <td style="vertical-align: middle;"><h4>Generar nómina</h4></td>
                           <td style="vertical-align: middle;width: 40%;">
                               <div class="input-group">
                                   <span class="input-group-addon">Seleccione una fecha</span>
                                   <input type="date" id="fecha_nomina" name="fecha_nomina" class="form-control">
                                   <span class="input-group-btn">
                                        <button class="btn btn-primary" type="button" onclick="generar_nomina()">
                                            <i class="fa fa-cog" ></i> Generar
                                        </button>
                                    </span>
                               </div>
                           </td>
                           <td class="text-right" style="vertical-align: middle;">
                               <button type="button" id="a_aprobar_nomina" onclick="aprobar_nomina()"
                                  data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Acción"
                                  data-content="Al hacer click se guardaran todos los datos correspondientes en la base de datos y se generará la nómina"
                                  style="cursor:pointer;visibility: hidden;" aria-hidden="true" class="btn btn-success ">
                                   <i class="fa fa-check-square-o" aria-hidden="true"></i>
                                   Aprobar nómina
                               </button>
                           </td>
                       </tr>
                   </table>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                   <div class="col-md-12 listado_nomina">
                       <label class="alert alert-info text-center" style="width: 100%">Seleccione una fecha para generar la nómina</label>
                   </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>

@endsection

@section('custom_page_js')
    @include('layouts.views.nomina.script')
@endsection
