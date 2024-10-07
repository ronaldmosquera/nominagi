<form id="form_add_productos" name="form_add_productos" enctype="multipart/form-data" novalidate>
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <h3 class="box-title">Seleccione un archivo excel</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="input-group ">
                        <input type="file" name="archivo" id="archivo" class="form-control" accept="file/.xls,.xlsx" required >
                    </div>
                </div>
                <div class="col-md-12" style="padding-top: 10px">
                    <button type="button" class="btn btn-info pull-right" onclick="store_productos()">
                        <i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@section('custom_page_js')
    @include('layouts.views.productos.script')
@endsection
