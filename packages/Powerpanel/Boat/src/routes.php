<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/boat', 'Powerpanel\Boat\Controllers\Powerpanel\BoatController@index')->name('powerpanel.boat.index');
    Route::post('powerpanel/boat/get_list/', 'Powerpanel\Boat\Controllers\Powerpanel\BoatController@get_list');
    Route::post('powerpanel/boat/publish', ['uses' => 'Powerpanel\Boat\Controllers\Powerpanel\BoatController@publish', 'middleware' => 'permission:' . Config::get('Constant.MODULE.NAME') . '-edit']);
    Route::get('powerpanel/boat/reorder/', 'Powerpanel\Boat\Controllers\Powerpanel\BoatController@reorder')->name('powerpanel.boat.reorder');
    Route::get('powerpanel/boat/add', 'Powerpanel\Boat\Controllers\Powerpanel\BoatController@edit')->name('powerpanel.boat.add');
    Route::post('powerpanel/boat/add/', 'Powerpanel\Boat\Controllers\Powerpanel\BoatController@handlePost')->name('powerpanel.boat.handleAddPost');
    Route::get('powerpanel/boat/{alias}/edit', 'Powerpanel\Boat\Controllers\Powerpanel\BoatController@edit')->name('powerpanel.boat.edit');
    Route::post('powerpanel/boat/{alias}/edit', 'Powerpanel\Boat\Controllers\Powerpanel\BoatController@handlePost')->name('powerpanel/boat/handleEditPost');
    Route::post('powerpanel/boat/DeleteRecord', 'Powerpanel\Boat\Controllers\Powerpanel\BoatController@DeleteRecord');
    Route::post('powerpanel/boat/get_builder_list', ['uses' => 'Powerpanel\Boat\Controllers\Powerpanel\BoatController@get_builder_list']);
    Route::post('powerpanel/boat-category/get_builder_list', ['uses' => 'Powerpanel\BoatCategory\Controllers\Powerpanel\BoatCategoryController@get_builder_list']);
});
