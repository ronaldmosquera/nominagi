<?php
    Route::get('borrar-cache',function (){
        Artisan::call('cache:clear');
        Artisan::call('config:cache');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        echo "cache del sistema borrada...!!!";
    });

    Route::post('configuracion-empresa/store_variables', 'ConfiguracionEmpresaController@storeConfiguracionVariables')->name('configuracion-empresa-variables.store');
    Route::get('vista-cargo', 'CargoController@vistaFormContrato')->name('vista.form_cargos');
    Route::get('vista-contrato', 'TipoContratoController@vistaFormContrato')->name('vista.form_contrato');
    Route::get('proyeccion_nomina/form-proyeccion','ProyeccionNominaController@formProyeccion')->name('form-proyeccion');
    Route::post('actualizar-documento', 'DocumentosController@actualizarDocumento')->name('actualizar.documento');
    Route::get('estadisticas/nomina/{id}','NominaController@show');
    Route::get('estadisticas-nomina','NominaController@estadisticaNomina')->name('vista.estadisticas_nomina');
    Route::get('permisos','PermisoController@inicio');
    Route::get('permisos/ver_seccion','PermisoController@ver_seccion');
    Route::post('guardar-permisos', 'PermisoController@storePermiso');
    Route::post('eliminar-permisos', 'PermisoController@deletePermiso');
    Route::post('tipo-contrato/update-estatus', 'TipoContratoController@updateStatus');
    Route::post('tipo-contrato/delete', 'TipoContratoController@deleteTipoContrato');
    Route::post('contrato/update-estatus', 'ContratoController@updateStatus');
    Route::post('contrato/delete', 'ContratoController@deleteContrato');
    Route::post('cargo/delete', 'CargoController@deleteCargo');

//
