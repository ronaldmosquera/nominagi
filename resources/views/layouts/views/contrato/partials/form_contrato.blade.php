@extends('layouts.principal')
@section('title')
    Crear contrato
@endsection

@section('content')
    <form id="form_add_contrato" name="form_add_contrato">
        <input type="hidden" value="{!! !empty($dataContrato->id_contrato) ? $dataContrato->id_contrato : ''!!}" id="id_contrato">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Crear contrato</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="form-inline" style="padding: 10px;">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3" style="background: #d9d9d9;"> <i class="fa fa-file-text" aria-hidden="true"></i> Tipo de contrato</span>
                                <select class="form-control" id="id_tipo_contrato" name="id_tipo_contrato" required>
                                    <option selected disabled>Seleccione</option>
                                    @foreach($dataTipoContratos as $tipoContratos)
                                        @php
                                            $selected= '';
                                            if(!empty($dataContrato->id_tipo_contrato)){
                                                if($dataContrato->id_tipo_contrato === $tipoContratos->id_tipo_contrato){
                                                 $selected = 'selected="selected"';
                                                }
                                            }
                                        @endphp
                                        <option {{$selected}} value="{{$tipoContratos->id_tipo_contrato}}">{{$tipoContratos->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                <div>
            </div>
            <div class="form-group" style="padding: 10px;">
                <label>Tags para agregar información personalizada:</label> <br />
                <ul>
                    <li>
                        <label>Datos Empresa:</label> [NOMBRE_EMPRESA], [ID_EMPRESA], [DIREC_EMPRESA]
                    </li>
                    <li>
                        <label>Datos Representante de la empresa: </label> [NOMBRE_REP_EMPRESA], [ID_REP_EMPRESA]
                    </li>
                    <li>
                        <label>Datos empleado: </label> [NOMBRE_EMPLEADO], [ID_EMPLEADO], [NACIONALIDAD], [DIREC_EMPLEADO], [CARGO_EMPLEADO], [SALARIO_EMPLEADO], [HORAS_TRABAJO], [SALARIO_LETRAS], [CORREO], [FUNCIONES]
                    </li>
                    <li>
                        <label>Datos de fecha actual: </label> [D_ACTUAL], [M_ACTUAL], [A_ACTUAL]
                    </li>
                    <li>
                        <label>Datos fecha de contratación: </label> [D_CONTRATACION], [M_CONTRATACION], [A_CONTRATACION], [TIEMPO_CONTRATACION]
                    </li>
                    <li>
                        <label>Salto de página: </label> [SALTO_DE_PAGINA]
                    </li>
                    <li>
                        <label>Otros: </label> [CIUDAD]
                    </li>
                </ul>
                <textarea required class="ckeditor" name="body_contrato"  id="body_contrato" rows="10" cols="180">{!! !empty($dataContrato->cuerpo_contrato) ? $dataContrato->cuerpo_contrato : ''!!}</textarea>
            </div>
                </div>
                <button type="button" class="btn btn-block btn-success btn-lg" onclick="store_contrato()"><i id="ico" class="fa fa-floppy-o" aria-hidden="true"></i> Guardar</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('custom_page_js')
    @include('layouts.views.contrato.script')
@endsection
