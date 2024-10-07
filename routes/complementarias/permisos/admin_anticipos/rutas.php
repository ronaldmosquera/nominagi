<?php

    Route::get('admin-anticipos','AdminAnticiposController@adminAnticipos')->name('vista.admin_anticipos');
    Route::get('edit-anticipo-admin','AdminAnticiposController@editAnticipoAdmin')->name('vista.edit_anticipo_admin');
    Route::post('store-anticipo-admin','AdminAnticiposController@storeAnticipoAdmin')->name('store.anticipo_admin');
    Route::post('aprobar-anticipo-admin','AdminAnticiposController@aprobarAnticipo')->name('aprobar.anticipo');
    Route::get('form-comentario-anticipo-no-aprobado', 'AdminAnticiposController@formComentarioAnticipoNoAprobado')->name('vista.form_comentario_anticipo_no_aprobado');
    Route::post('store-comentario-anticipo-no-aprobado', 'AdminAnticiposController@storeComentarioAnticipoNoAprobado')->name('store.comentario_anticipo_no_aprobado');
    Route::post('form-cash-management-anticipos', 'AdminAnticiposController@formCashManagementAnticipo')->name('form.cash-management-anticipos');
    Route::post('download-cash-management-anticipo', 'AdminAnticiposController@downloadCashManagementAnticipo')->name('download.cash-management-anticipo');
    Route::post('store-referencia-bancaria-anticipos', 'AdminAnticiposController@storeReferenciaBancariaAnticipo')->name('store.referencia-bancaria-anticipos');
