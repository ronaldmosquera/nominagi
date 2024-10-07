<div class="">
    @if($estadoContrato->estado != 0 && $estadoContrato->estado != 2 && $estadoContrato->estado != 3)
        <form id="form_add_imagen_contrataciones"  class="{{count($imagenesContrataciones)>0 ? 'hide' : 'show'}}" enctype="multipart/form-data" novalidate="novalidate">
            <input type="hidden" id="idContratacion" value="{{$idContratacion}}">
            <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
                <div class="box-header with-border">
                    <h3 class="box-title">Suba lo(s) archivo(s) correspondientes</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <input type="file" class="form-control" name="file[]" id="file" multiple required accept="image/.jpg,.png,.JPG,.PNG">
                        </div>
                    </div>
                    <div class="col-md-12" style="padding-top: 10px">
                        <button type="button" class="btn btn-info pull-right" id="btn_upload_contrataciones_firmadas" onclick="upload_contratacion_firmada()">
                            <i id="ico" class="fa fa-floppy-o"></i>
                            Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    @else
        @if(count($imagenesContrataciones)<1)
            <div class="alert alert-danger" role="alert">
           No existen imágenes para mostrar
        </div>
        @endif
    @endif
</div>
@if(count($imagenesContrataciones)>0)
<div id="imagenes" class="row">
        @foreach($imagenesContrataciones as $imagenes)
            @php
                $extension = explode(".",$imagenes->imagen);
                $cantExt = count($extension);
            @endphp
            <div class="col-md-4">
                <button type="button" class="close">
                    <span aria-hidden="true" title="Eliminar" onclick="eliminar_imagen_contrataciones('{{$imagenes->id_imagenes_detalles_contrataciones}}','{{$imagenes->imagen}}')" data-toggle="tooltip">&times;</span>
                </button>
                @if($extension[$cantExt-1] === "pdf")
                    <a target="_blank" style="width: 100%" title="Documento" href="{{asset('imagenes_contratos/'.$imagenes->imagen)}}" class="btn btn-primary btn-lg">
                        <i class="fa fa-4x fa-file-pdf-o"></i>
                    </a>

                @else
                    <a href="{{asset("imagenes_contratos/".$imagenes->imagen)}}" title="Imagen" data-lightbox="image-1" data-lightbox="roadtrip">
                    <img class="img-thumbnail" width="304" height="236" src="{{asset("imagenes_contratos/".$imagenes->imagen)}}">
                    </a>
                @endif
            </div>

        @endforeach
</div>
    @if($estadoContrato->estado != 0 && $estadoContrato->estado != 2 && $estadoContrato->estado != 3)
    <div class="row well" style="margin: 16px 0px;">
        <div class="col-md-12">
            <input type="checkbox" id="agregar" name="agregar"  onclick="formulario()">
            <label for="agregar">¿Desea agrega más imagenes?</label>
        </div>
    </div>
    @endif
@endif


<script> $('[data-toggle="tooltip"]').tooltip();</script>