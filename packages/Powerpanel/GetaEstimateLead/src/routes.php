<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/get-a-estimate/', 'Powerpanel\GetaEstimateLead\Controllers\Powerpanel\GetaEstimateLeadController@index')->name('powerpanel.get-a-estimate.list');
    Route::get('powerpanel/get-a-estimate/', 'Powerpanel\GetaEstimateLead\Controllers\Powerpanel\GetaEstimateLeadController@index')->name('powerpanel.get-a-estimate.index');
    
    Route::post('powerpanel/get-a-estimate/get_list/', 'Powerpanel\GetaEstimateLead\Controllers\Powerpanel\GetaEstimateLeadController@get_list')->name('powerpanel.get-a-estimate.get_list');
   Route::get('/powerpanel/get-a-estimate/ExportRecord', ['uses' => 'Powerpanel\GetaEstimateLead\Controllers\Powerpanel\GetaEstimateLeadController@ExportRecord', 'middleware' => 'permission:get-a-estimate-list']);
   Route::post('powerpanel/get-a-estimate/DeleteRecord', 'Powerpanel\GetaEstimateLead\Controllers\Powerpanel\GetaEstimateLeadController@DeleteRecord');
       
   Route::get('powerpanel/get-a-estimate/{email}', 'Powerpanel\GetaEstimateLead\Controllers\Powerpanel\GetaEstimateLeadController@index')->name('powerpanel.get-a-estimate.index');
});
