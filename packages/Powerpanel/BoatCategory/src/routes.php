<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/boat-category', 'Powerpanel\BoatCategory\Controllers\Powerpanel\BoatCategoryController@index')->name('powerpanel.boat-category.index');
    Route::post('powerpanel/boat-category/get_list/', 'Powerpanel\BoatCategory\Controllers\Powerpanel\BoatCategoryController@get_list');
    Route::post('powerpanel/boat-category/publish', ['uses' => 'Powerpanel\BoatCategory\Controllers\Powerpanel\BoatCategoryController@publish', 'middleware' => 'permission:' . Config::get('Constant.MODULE.NAME') . '-edit']);
    Route::get('powerpanel/boat-category/reorder/', 'Powerpanel\BoatCategory\Controllers\Powerpanel\BoatCategoryController@reorder')->name('powerpanel.boat-category.reorder');
    Route::get('powerpanel/boat-category/add', 'Powerpanel\BoatCategory\Controllers\Powerpanel\BoatCategoryController@edit')->name('powerpanel.boat-category.add');
    Route::post('powerpanel/boat-category/add/', 'Powerpanel\BoatCategory\Controllers\Powerpanel\BoatCategoryController@handlePost')->name('powerpanel.boat-category.handleAddPost');
    Route::get('powerpanel/boat-category/{alias}/edit', 'Powerpanel\BoatCategory\Controllers\Powerpanel\BoatCategoryController@edit')->name('powerpanel.boat-category.edit');
    Route::post('powerpanel/boat-category/{alias}/edit', 'Powerpanel\BoatCategory\Controllers\Powerpanel\BoatCategoryController@handlePost')->name('powerpanel/boat-category/handleEditPost');
    Route::post('powerpanel/boat-category/DeleteRecord', 'Powerpanel\BoatCategory\Controllers\Powerpanel\BoatCategoryController@DeleteRecord');
});
