<?php

Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/roles/', 'Powerpanel\RoleManager\Controllers\Powerpanel\RoleController@index')->name('powerpanel.roles.list');
    Route::get('powerpanel/roles/', 'Powerpanel\RoleManager\Controllers\Powerpanel\RoleController@index')->name('powerpanel.roles.index');

    Route::post('powerpanel/roles/get_list/', 'Powerpanel\RoleManager\Controllers\Powerpanel\RoleController@get_list')->name('powerpanel.roles.get_list');

    Route::post('powerpanel/roles/DeleteRecord', 'Powerpanel\RoleManager\Controllers\Powerpanel\RoleController@DeleteRecord');
    Route::post('powerpanel/roles/publish', ['uses' => 'Powerpanel\RoleManager\Controllers\Powerpanel\RoleController@publish', 'middleware' => 'permission:roles-edit']);
    Route::post('powerpanel/roles/reorder', ['as' => Config::get('Constant.MODULE.NAME') . '.reorder', 'uses' => 'Powerpanel\RoleManager\Controllers\Powerpanel\RoleController@reorder', 'middleware' => 'permission:roles-list']);

    Route::get('/powerpanel/roles/show/{id}', ['as' => 'powerpanel.roles.show', 'uses' => 'Powerpanel\RoleManager\Controllers\Powerpanel\RoleController@show']);
    Route::patch('/powerpanel/roles/{id}', ['as' => 'powerpanel.roles.update', 'uses' => 'Powerpanel\RoleManager\Controllers\Powerpanel\RoleController@handlePost', 'middleware' => ['permission:roles-edit']]);
});
