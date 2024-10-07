<?php

    Route::get('roles-pago','NominaController@reporteRolesPago')->name('vista.roles-pago');
    Route::get('form-nivelar-nomina','NominaController@formnivelarNomina')->name('form-nivelar-nomina');
    Route::post('nivelar-nomina','NominaController@nivelarNomina')->name('nivelar-nomina');
    Route::get('ver-nomina', 'NominaController@vistaNomina')->name('vista.listado_nomina');
    Route::get('generar_nomina', 'NominaController@generaNomina')->name('generar.nomina');
    Route::get('informe-nomina', 'NominaController@informeNomina')->name('informe.nomina');
    Route::post('file-cash-managment', 'NominaController@fileCashManagment')->name('file.cash_managment');
    Route::post('generar-informe-nomina', 'NominaController@generaInformeNomina')->name('genera.informe_nomina');
    Route::get('crear-alcance-nomina', 'NominaController@crearAlcanceNomina')->name('genera.crear-alcance-nomina');
    Route::post('store-alcance-nomina', 'NominaController@storeAlcanceNomina')->name('genera.store-alcance-nomina');
    Route::post('form-cash-management', 'NominaController@formCashManagement');
    Route::post('store-referencia-bancaria', 'NominaController@storeReferenciaBancaria');
    Route::post('form-cash-management-decimos', 'NominaController@formCashManagementDecimos');
    Route::post('file-cash-managment-decimos', 'NominaController@fileCashManagmentDecimos');
    Route::post('store-referencia-bancaria-decimos', 'NominaController@storeReferenciaBancariaDecimos');
    Route::post('form-cash-management-alcance-nomina', 'NominaController@formCashManagementAlcanceNomina');
    Route::post('file-cash-managment-alcance-nomina', 'NominaController@fileCashManagmentAlcanceNomina');
    Route::post('store-referencia-bancaria-alcance-nomina', 'NominaController@storeReferenciaBancariaAlcanceNomina');
    Route::post('form-cash-management-liquidacion', 'NominaController@formCashManagementLiquidacion');
    Route::post('file-cash-managment-liquidacion', 'NominaController@fileCashManagmentLiquidacion');
    Route::post('store-referencia-bancaria-liquidacion', 'NominaController@storeReferenciaBancariaLiquidacion');
