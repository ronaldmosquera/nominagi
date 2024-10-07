@extends('layouts.principal')
@section('title')
    Documentos
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Solicitud de documentos</h3>
                </div>
                <div class="box-body table-responsive no-padding" style="margin-top: 15px">
                  @foreach($dataDocumentos as $documentos)
                    <div class="col-md-3 col-xs-6 text-center">
                        @if($documentos->tipo_documento =='TRANSCRITO')
                            <a href="{{route('vista.generar_documento',$documentos->id_documentos)}}"
                                    title="Generar documento" class="btn btn-success btn-lg">
                                <i class="fa fa-4x fa-file-pdf-o" aria-hidden="true"></i>
                            </a><br />
                        @else
                            <a target="_blank" href="/config_empresa/{{$documentos->file}}"
                                title="Ver documento" class="btn btn-success btn-lg">
                                <i class="fa fa-4x fa-file-pdf-o" aria-hidden="true"></i>
                            </a><br />
                        @endif
                        <label>{{ucwords($documentos->nombre)}}</label>
                    </div>
                  @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection


@section('custom_page_js')
    @include('layouts.views.documentos.script')
@endsection
