<?php
    Route::get('descuentos/add_inputs','OtrosDescuentosController@addInputs')->name('vista.add_inputs_descuentos');
    Route::post('delete_otros_descuentos', 'OtrosDescuentosController@deleteDescuento')->name('delete.otros_descuentos');
    Route::post('store_otros_descuentos', 'OtrosDescuentosController@store')->name('store.otros_descuentos');
    Route::get('get_concepto_descuento', 'OtrosDescuentosController@getConceptoDescuentos')->name('get.concepto_descuentos');
