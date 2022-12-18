<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/service-inquiry/', 'Powerpanel\ServiceInquiryLead\Controllers\Powerpanel\ServiceinquiryLeadController@index')->name('powerpanel.service-inquiry.list');
    Route::get('powerpanel/service-inquiry/', 'Powerpanel\ServiceInquiryLead\Controllers\Powerpanel\ServiceinquiryLeadController@index')->name('powerpanel.service-inquiry.index');
    
    Route::post('powerpanel/service-inquiry/get_list/', 'Powerpanel\ServiceInquiryLead\Controllers\Powerpanel\ServiceinquiryLeadController@get_list')->name('powerpanel.service-inquiry.get_list');
   Route::get('/powerpanel/service-inquiry/ExportRecord', ['uses' => 'Powerpanel\ServiceInquiryLead\Controllers\Powerpanel\ServiceinquiryLeadController@ExportRecord', 'middleware' => 'permission:service-inquiry-list']);
   Route::post('powerpanel/service-inquiry/DeleteRecord', 'Powerpanel\ServiceInquiryLead\Controllers\Powerpanel\ServiceinquiryLeadController@DeleteRecord');

   Route::get('powerpanel/service-inquiry/{email}', 'Powerpanel\ServiceInquiryLead\Controllers\Powerpanel\ServiceinquiryLeadController@index')->name('powerpanel.service-inquiry.index');
       
});
