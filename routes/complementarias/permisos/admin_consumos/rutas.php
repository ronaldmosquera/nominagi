<?php
    Route::get('admin-consumos','AdminConsumosController@adminConsumos')->name('vista.admin_consumos');
    Route::get('form-admin-consumos','AdminConsumosController@formAdminConsumos')->name('form.admin_consumos');
    Route::post('store-admin-consumos','AdminConsumosController@storeAdminConsumo')->name('store.admin_consumo');
    Route::get('form-comentario-consumo-no-aprobado', 'AdminConsumosController@formComentarioConsumoNoAprobado')->name('vista.form_comentario_consumo_no_aprobado');
    Route::post('store-comentario-consumo-no-aprobado', 'AdminConsumosController@storeComentarioConsumoNoAprobado')->name('store.comentario_consumo_no_aprobado');
    Route::get('edit-consumo-admin', 'AdminConsumosController@editConsumoAdmin')->name('vista.edit_consumo_admin');
    Route::post('store-consumo-admin', 'AdminConsumosController@storeConsumoAdmin')->name('store.consumo_admin');
    Route::post('aprobar-consumo-admin', 'AdminConsumosController@aprobarConsumo')->name('aprobar.consumo');