<?php
    Route::post('horarios/store_intervalo', 'HorariosEmpleadosController@storeIntervaloHora')->name('store.horarios_intervalos');
    Route::post('horarios/delete_intervalo', 'HorariosEmpleadosController@deleteIntervaloHora')->name('delete.horarios_intervalos');
    Route::get('obtener_horarios', 'HorariosEmpleadosController@obtenerHorarios')->name('vista.obtener_horarios');
