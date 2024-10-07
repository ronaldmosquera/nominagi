<?php

    Route::get('add_inputs_horas_extra', 'HoraExtraController@add_inputs')->name('vista.add_inputs_horas_extra');
    Route::get('dias-vacaciones', 'VacacionesController@diasVacaciones');
    Route::get('documentos-empleado','DocumentosEmpleadoController@index')->name('vista.solicitud_documentos');
    Route::get('generar-documento/{id_documento?}','DocumentosEmpleadoController@generarDocumento')->name('vista.generar_documento');
    Route::get('add_inputs', 'ConsumosController@addInputs')->name('vista.add_inputs');
    Route::get('costo-productos','ProductosController@obtenerCostoProducto')->name('producto.costo');
    //Route::get('comisiones/list', 'ComisionesController@listComisiones')->name('vista.comisiones_empleado');
    Route::get('descuentos-empleado','OtrosDescuentosEmpleadoController@listDescuentos')->name('vista.descuentos_empleados');
    Route::get('comisiones-empleado', 'UsuarioController@comisionesEmpleado')->name('vista.comisiones_empleado');
    Route::get('roles-empleado','UsuarioController@rolesEmpleado')->name('vista.roles_pago_empleado');
    Route::get('nomina-empleado','NominaEmpleadoController@estadisticaNominaEmpleado')->name('vista.nomia_empleado');
    Route::get('empleado/contrataciones','ContratacionesEmpleadoController@index')->name('vista.contrataciones');
    Route::get('vista-contratacion-firmada-empleado', 'ContratacionesEmpleadoController@addContratacionEmpleado')->name('vista.imagen_contratacion_firamda_empleado');
    Route::post('delete-hora-extra', 'HoraExtraController@delete_hora_extra')->name('delete.hora-extra');
    Route::post('delete-anticipos', 'AnticiposController@deleteAnticipo')->name('delete.anticipos');
    Route::get('search-feriado', 'HoraExtraController@search_feriado')->name('search.feriado');
    Route::post('delete-vacaciones', 'VacacionesController@deleteVacaciones')->name('delete.vacaiones');
