<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/data-removal-lead/', 'Powerpanel\DataRemovalLead\Controllers\Powerpanel\DataRemovalLeadController@index')->name('powerpanel.data-removal-lead.list');
    Route::get('powerpanel/data-removal-lead/', 'Powerpanel\DataRemovalLead\Controllers\Powerpanel\DataRemovalLeadController@index')->name('powerpanel.data-removal-lead.index');
    
    Route::post('powerpanel/data-removal-lead/get_list/', 'Powerpanel\DataRemovalLead\Controllers\Powerpanel\DataRemovalLeadController@get_list')->name('powerpanel.data-removal-lead.get_list');
   Route::get('/powerpanel/data-removal-lead/ExportRecord', ['uses' => 'Powerpanel\DataRemovalLead\Controllers\Powerpanel\DataRemovalLeadController@ExportRecord', 'middleware' => 'permission:data-removal-lead-list']);
   Route::post('powerpanel/data-removal-lead/DeleteRecord', 'Powerpanel\DataRemovalLead\Controllers\Powerpanel\DataRemovalLeadController@DeleteRecord');
       
});
