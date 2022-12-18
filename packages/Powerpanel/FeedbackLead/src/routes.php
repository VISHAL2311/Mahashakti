<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/formbuilder-lead/', 'Powerpanel\FeedbackLead\Controllers\Powerpanel\FeedbackleadController@index')->name('powerpanel.formbuilder-lead.list');
    Route::get('powerpanel/formbuilder-lead/', 'Powerpanel\FeedbackLead\Controllers\Powerpanel\FeedbackleadController@index')->name('powerpanel.formbuilder-lead.index');
    
    Route::post('powerpanel/formbuilder-lead/get_list/', 'Powerpanel\FeedbackLead\Controllers\Powerpanel\FeedbackleadController@get_list')->name('powerpanel.formbuilder-lead.get_list');
   Route::get('/powerpanel/formbuilder-lead/ExportRecord', ['uses' => 'Powerpanel\FeedbackLead\Controllers\Powerpanel\FeedbackleadController@ExportRecord', 'middleware' => 'permission:formbuilder-lead-list']);
   Route::post('powerpanel/formbuilder-lead/DeleteRecord', 'Powerpanel\FeedbackLead\Controllers\Powerpanel\FeedbackleadController@DeleteRecord');
       
});
