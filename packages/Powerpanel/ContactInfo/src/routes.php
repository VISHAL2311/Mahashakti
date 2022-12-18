<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/contact-info/', 'Powerpanel\ContactInfo\Controllers\Powerpanel\ContactInfoController@index')->name('powerpanel.contact-info.list');
    Route::get('powerpanel/contact-info/', 'Powerpanel\ContactInfo\Controllers\Powerpanel\ContactInfoController@index')->name('powerpanel.contact-info.index');
    
    Route::post('powerpanel/contact-info/get_list/', 'Powerpanel\ContactInfo\Controllers\Powerpanel\ContactInfoController@get_list')->name('powerpanel.contact-info.get_list');
   
});
