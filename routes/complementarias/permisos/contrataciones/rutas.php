<?php

Route::get('campos-obligatorios', 'ContratacionesController@camposObligatorios')->name('vista.campos_obligatorios');
Route::get('campos-sin-relacion-dependencia', 'ContratacionesController@camposSinRelacionDependencia')->name('vista.relacion_sin_dependencia');
Route::get('campos-relacion-dependencia', 'ContratacionesController@camposRelacionDependencia')->name('vista.relacion_dependencia');
Route::post('terminar-contrataciones', 'ContratacionesController@terminarContrato')->name('terminar-contratacion.store');
Route::get('anular-contrataciones', 'ContratacionesController@anularContrato')->name('anular_contratacion.store');
Route::post('vista-contratacion-firmada', 'ContratacionesController@addContratacion')->name('vista.imagen-contratacion');
Route::post('upload-contratacion-firmada', 'ContratacionesController@uploadImagenContratacion')->name('upload.imagenes-contratacion');
Route::post('delete-contratacion-firmada', 'ContratacionesController@deleteImagenContratacion')->name('delete.imagenes-contratacion');
Route::post('inputs-empleado','ContratacionesController@inputsEmpleados')->name('vista.inputs_empleado');
Route::get('form-terminacion-contrato','ContratacionesController@formTerminacionContrato')->name('vista.form_terminacion_contrato');
Route::post('update-contratacion','ContratacionesController@updateContratacion');
Route::get('numero-letras','ContratacionesController@letras');
Route::get('valida-sueldo-sectorial','ContratacionesController@validaSueldoSectorial')->name('valida-sueldo-sectorial');
Route::get('form-addendum','ContratacionesController@add_addendum')->name('vista.add_addemdun');
Route::get('form-bonos-fijos','ContratacionesController@formBonosFijos')->name('vista.bonos_fijos');
Route::get('inputs-bonos-fijos','ContratacionesController@inputsBonosFijos')->name('vista.inputs_bonos_fijos');
Route::get('inputs-prestamos','ContratacionesController@inputsPrestamos')->name('vista.inputs_prestamos');
Route::post('store-bonos-fijos','ContratacionesController@storeBonosFijos')->name('store.bonos_fijos');
Route::post('delete-bonos-fijos','ContratacionesController@deleteBonosFijos')->name('delete.bono_fijo');
Route::post('store-prestamo','ContratacionesController@storePrestamo')->name('store.prestamo');
Route::post('store-addendum-contratacion','ContratacionesController@storeAddendumContrataciones')->name('store.addendum_contrataciones');
Route::post('update-detalle-contratacion','ContratacionesController@updateDetalleContratacion')->name('update.detalle_contratacion');
Route::post('eliminar-prestamo','ContratacionesController@deletePrestamo');
Route::post('form-cash-management-prestamos','ContratacionesController@formCashManagementPrestamo');
Route::post('download-cash-management-prestamos','ContratacionesController@downloadCashManagementPrestamo');
Route::post('store-referencia-bancaria-prestamos','ContratacionesController@storeReferenciaBancariaPrestamos');

