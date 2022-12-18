<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/contact-us/', 'Powerpanel\ContactUsLead\Controllers\Powerpanel\ContactLeadController@index')->name('powerpanel.contact-us.list');
    Route::get('powerpanel/contact-us/', 'Powerpanel\ContactUsLead\Controllers\Powerpanel\ContactLeadController@index')->name('powerpanel.contact-us.index');
    
    Route::post('powerpanel/contact-us/get_list/', 'Powerpanel\ContactUsLead\Controllers\Powerpanel\ContactLeadController@get_list')->name('powerpanel.contact-us.get_list');
   Route::get('/powerpanel/contact-us/ExportRecord', ['uses' => 'Powerpanel\ContactUsLead\Controllers\Powerpanel\ContactLeadController@ExportRecord', 'middleware' => 'permission:contact-us-list']);
   Route::post('powerpanel/contact-us/DeleteRecord', 'Powerpanel\ContactUsLead\Controllers\Powerpanel\ContactLeadController@DeleteRecord');

   Route::get('powerpanel/contact-us/{email}', 'Powerpanel\ContactUsLead\Controllers\Powerpanel\ContactLeadController@index')->name('powerpanel.contact-us.index');
       
});
