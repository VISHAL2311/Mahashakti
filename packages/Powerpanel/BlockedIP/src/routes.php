<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/blocked-ips/', 'Powerpanel\BlockedIP\Controllers\Powerpanel\BlockedIpsController@index')->name('powerpanel.blocked-ips.list');
    Route::get('powerpanel/blocked-ips/', 'Powerpanel\BlockedIP\Controllers\Powerpanel\BlockedIpsController@index')->name('powerpanel.blocked-ips.index');
    
    Route::post('powerpanel/blocked-ips/get-list/', 'Powerpanel\BlockedIP\Controllers\Powerpanel\BlockedIpsController@get_list')->name('powerpanel.blocked-ips.get_list');
   Route::get('/powerpanel/blocked-ips/ExportRecord', ['uses' => 'Powerpanel\BlockedIP\Controllers\Powerpanel\BlockedIpsController@ExportRecord', 'middleware' => 'permission:blocked-ips-list']);
   Route::post('powerpanel/blocked-ips/DeleteRecord', 'Powerpanel\BlockedIP\Controllers\Powerpanel\BlockedIpsController@DeleteRecord');
       
});
