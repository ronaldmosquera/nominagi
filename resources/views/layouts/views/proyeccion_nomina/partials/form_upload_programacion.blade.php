<form id="form_add_programacion" name="form_add_programacion" enctype="multipart/form-data" novalidate>
    <div class="box box-info" style=" box-shadow: 0px -1px 75px -21px grey;">
        <div class="box-header with-border">
            <h3 class="box-title">Cargue la proyección de la nómina</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div>
                        <label for="excel">Programación</label>
                        <input type="file" name="archivo" id="archivo" class="form-control" accept="file/.xls,.xlsx" required >
                    </div>
                </div>
                <div class="col-md-6">
                    <div>
                        <label for="anno">Año</label>
                        <input type="text" name="anno" id="anno" class="form-control Date" required >
                    </div>
                </div>
                <div class="col-md-12" style="padding-top: 10px">
                    <button type="button" class="btn btn-info pull-right" onclick="store_proyeccion()">
                        <i id="ico" class="fa fa-floppy-o" aria-hidden="true" ></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $('.Date').datepicker({
        format: 'yyyy',
        //endDate: '0d',
        //startDate: '0d',
        language: 'es-ES'
    });
</script>