<?php

    Route::post('vista_datos_faltantes', 'EmpleadoController@datosFaltantes')->name('vista.datos_faltantes');
    Route::post('update_data_empleado', 'EmpleadoController@updateDataEmpleado')->name('update.data_empleado');

