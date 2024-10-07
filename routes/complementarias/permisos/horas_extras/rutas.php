<?php
    Route::get('administrar-horas-extras','HoraExtraController@adminHorasExtras')->name('vista.list_horas_extras_admin');
    Route::post('store-comentario-horas-extras','HoraExtraController@storeRespuestaComentario')->name('respuesta.store_respuesta_comentario');
    Route::get('responder-comentario-horas-extras','HoraExtraController@responderComentario')->name('vista.responder_comentario_horas_extras');
    Route::post('administrar-horas-extras/aprobar','HoraExtraController@update')->name('vista.success_horas_extras_admin');
    Route::get('horas_extras/vista-configurar-feriados','HoraExtraController@configurar_feriados')->name('vista.configurar_feriados');
    Route::get('horas_extras/vista-imput-feriados','HoraExtraController@input_feriados')->name('vista.input_feriados');
    Route::post('store-fecha_feriado', 'HoraExtraController@store_fecha_feriado')->name('store.fecha_feriado');
    Route::get('search-anno-mes-feriado', 'HoraExtraController@search_anno_mes_feriado')->name('search.anno-mes-feriado');