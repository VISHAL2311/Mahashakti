<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/team', 'Powerpanel\Team\Controllers\Powerpanel\TeamController@index')->name('powerpanel.team.index');
    Route::post('powerpanel/team/get_list/', 'Powerpanel\Team\Controllers\Powerpanel\TeamController@get_list');
    Route::post('powerpanel/team/publish', ['uses' => 'Powerpanel\Team\Controllers\Powerpanel\TeamController@publish', 'middleware' => 'permission:' . Config::get('Constant.MODULE.NAME') . '-edit']);
    Route::get('powerpanel/team/reorder/', 'Powerpanel\Team\Controllers\Powerpanel\TeamController@reorder')->name('powerpanel.team.reorder');
    Route::get('powerpanel/team/add', 'Powerpanel\Team\Controllers\Powerpanel\TeamController@edit')->name('powerpanel.team.add');
    Route::post('powerpanel/team/add/', 'Powerpanel\Team\Controllers\Powerpanel\TeamController@handlePost')->name('powerpanel.team.handleAddPost');
    Route::get('powerpanel/team/{alias}/edit', 'Powerpanel\Team\Controllers\Powerpanel\TeamController@edit')->name('powerpanel.team.edit');
    Route::post('powerpanel/team/{alias}/edit', 'Powerpanel\Team\Controllers\Powerpanel\TeamController@handlePost')->name('powerpanel/team/handleEditPost');
    Route::post('powerpanel/team/DeleteRecord', 'Powerpanel\Team\Controllers\Powerpanel\TeamController@DeleteRecord');
    Route::post('powerpanel/team/get_builder_list', ['uses' => 'Powerpanel\Team\Controllers\Powerpanel\TeamController@get_builder_list']);
});
