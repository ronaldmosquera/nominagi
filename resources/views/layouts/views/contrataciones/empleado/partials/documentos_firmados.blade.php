<div class="row">
@foreach($contratacionesFirmadas as $contratacionFirmada)

    <div class="col-md-4" style="margin: 10px 0">
        <a href="{{asset("imagenes_contratos/".$contratacionFirmada->imagen)}}" data-lightbox="image-1" data-lightbox="roadtrip">
            <img class="img-thumbnail" width="304" height="236" src="{{asset("imagenes_contratos/".$contratacionFirmada->imagen)}}">
        </a>
    </div>

@endforeach
</div>