<?php
    Route::get('administrar-vacaciones','AdminVacacionesController@adminVacaciones')->name('vista.list_vacaciones_admin');
    Route::get('edit-vacaciones','AdminVacacionesController@editVacaciones')->name('vista.edit_vacaciones_admin');
    Route::post('store-edit-vacaciones','AdminVacacionesController@storeEditVacaciones')->name('store.edit_vacaciones_admin');
    Route::get('dias-vacaciones-admin', 'AdminVacacionesController@diasVacacionesAdmin');
    Route::post('administrar-vacaciones/aprobar','AdminVacacionesController@update')->name('vista.success_vacaciones_admin');
    Route::get('form-comentario-vacaciones-no-aprobadas', 'AdminVacacionesController@formComentarioVacacionesNoAprobadas')->name('vista.form_comentario_vacaciones_no_aprobadas');
    Route::post('store-comentario-vacaciones-no-aprobadas', 'AdminVacacionesController@storeComentarioVacacionesNoAprobadas')->name('store.comentario_vacaciones_no_aprobadas');