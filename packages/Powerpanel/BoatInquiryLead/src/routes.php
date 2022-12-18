<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/boat-inquiry/', 'Powerpanel\BoatInquiryLead\Controllers\Powerpanel\BoatinquiryLeadController@index')->name('powerpanel.boat-inquiry.list');
    Route::get('powerpanel/boat-inquiry/', 'Powerpanel\BoatInquiryLead\Controllers\Powerpanel\BoatinquiryLeadController@index')->name('powerpanel.boat-inquiry.index');
    
    Route::post('powerpanel/boat-inquiry/get_list/', 'Powerpanel\BoatInquiryLead\Controllers\Powerpanel\BoatinquiryLeadController@get_list')->name('powerpanel.boat-inquiry.get_list');
   Route::get('/powerpanel/boat-inquiry/ExportRecord', ['uses' => 'Powerpanel\BoatInquiryLead\Controllers\Powerpanel\BoatinquiryLeadController@ExportRecord', 'middleware' => 'permission:boat-inquiry-list']);
   Route::post('powerpanel/boat-inquiry/DeleteRecord', 'Powerpanel\BoatInquiryLead\Controllers\Powerpanel\BoatinquiryLeadController@DeleteRecord');

   Route::get('powerpanel/boat-inquiry/{email}', 'Powerpanel\BoatInquiryLead\Controllers\Powerpanel\BoatinquiryLeadController@index')->name('powerpanel.boat-inquiry.index');
       
});
