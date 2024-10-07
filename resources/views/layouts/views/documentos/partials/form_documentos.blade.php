@extends('layouts.principal')
@section('title')
    Crear documento
@endsection

@section('content')
    <div class="col-md-12">
        <form id="form_store_documento" name="form_store_documento">
            <div class="row" style="padding: 10px 0px">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;">Nombre documento</span>
                        <input type="text" name="nombre_documento" id="nombre_documento" value="{!! isset($dataDocumento->nombre) ?  $dataDocumento->nombre : '' !!}" class="form-control" required minlength="3">
                        <input type="hidden" id="id_documento" name="id_documento" value="{!! isset($dataDocumento->id_documentos) ?  $dataDocumento->id_documentos : '' !!}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;" >Tipo de documento</span>
                        <select id="tipo_documento" name="tipo_documento" class="form-control" onchange="tipo_doc()">
                            <option {{ isset($dataDocumento->tipo_documento) ? ($dataDocumento->tipo_documento == 'TRANSCRITO' ? 'selected' : '') : ''}} value="TRANSCRITO">Transcrito</option>
                            <option {{ isset($dataDocumento->tipo_documento) ? ($dataDocumento->tipo_documento == 'PDF' ? 'selected' : '') : ''}} value="PDF">PDF</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #D9D9D9;">Tipo de contración que usa el archivo</span>
                        <select id="relacion_dependencia" name="relacion_dependencia" class="form-control">
                            <option {{ isset($dataDocumento->relacion_dependencia) ? ($dataDocumento->relacion_dependencia == 1 ? 'selected' : '') : ''}} value="1">Bajo relación de dependencia</option>
                            <option {{ isset($dataDocumento->relacion_dependencia) ? ($dataDocumento->relacion_dependencia == 0 ? 'selected' : '') : ''}} value="0">Sin relación de dependencia</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="div_doc_prescrito" class="hidden">
                <div class="row" style="padding: 10px 10px">
                    <label>Tags para agregar información personalizada:</label>
                    <ul>
                        <li>
                            <label>Datos Empresa:</label> [NOMBRE_EMPRESA], [ID_EMPRESA], [DIREC_EMPRESA], [IMG_EMPRESA]
                        </li>
                        <li>
                            <label>Datos Representante de la empresa: </label> [NOMBRE_REP_EMPRESA], [ID_REP_EMPRESA]
                        </li>
                        <li>
                            <label>Datos empleado: </label> [NOMBRE_EMPLEADO], [ID_EMPLEADO], [DIREC_EMPLEADO], [CARGO_EMPLEADO], [SALARIO_EMPLEADO], [HORAS_TRABAJO], [HORAS_TRABAJO], [SALARIO_LETRAS]
                        </li>
                        <li>
                            <label>Datos de fecha: </label>  [D_ACTUAL], [M_ACTUAL], [A_ACTUAL]
                        </li>
                        <li>
                            <label>Salto de página: </label>  [SALTO_DE_PAGINA]
                        </li>
                    </ul>
                </div>
                <div class="row" style="padding: 10px 0px">
                    <div class="col-md-12">
                        <label>Cuerpo del documento</label>
                        <textarea class="ckeditor" name="cuerpo_documento"  id="cuerpo_documento" rows="10" cols="80">{!! isset($dataDocumento->cuerpo_documento) ?  $dataDocumento->cuerpo_documento : '' !!}</textarea>
                    </div>
                </div>
            </div>
            <div id="div_pdf" class="hidden" style="padding: 20px 0">
                <div style="margin-bottom: 10px">
                @if(isset($dataDocumento) && $dataDocumento->tipo_documento == 'PDF')
                    <a target="_blank" href="/config_empresa/{{$dataDocumento->file}}"><i class="fa fa-file-pdf-o"></i> VER ARCHIVO CARGADO</a>
                @endif
            </div>
                <label class=''>Ingrese el archivo pdf a subir</label>
                <input type="file" name="file" id="file" class="form-control" accept="application/pdf">
            </div>
            <button type="button" class="btn btn-block btn-success btn-lg" onclick="store_documento()">
                <i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i>
                Guardar
            </button>
        </form>
    </div>


@endsection

@section('custom_page_js')
    @include('layouts.views.documentos.script')
@endsection
