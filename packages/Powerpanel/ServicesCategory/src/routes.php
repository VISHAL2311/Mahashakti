<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/service-category', 'Powerpanel\ServicesCategory\Controllers\Powerpanel\ServiceCategoryController@index')->name('powerpanel.service-category.index');
    Route::post('powerpanel/service-category/get_list/', 'Powerpanel\ServicesCategory\Controllers\Powerpanel\ServiceCategoryController@get_list');
    Route::post('powerpanel/service-category/publish', ['uses' => 'Powerpanel\ServicesCategory\Controllers\Powerpanel\ServiceCategoryController@publish', 'middleware' => 'permission:' . Config::get('Constant.MODULE.NAME') . '-edit']);
    Route::get('powerpanel/service-category/reorder/', 'Powerpanel\ServicesCategory\Controllers\Powerpanel\ServiceCategoryController@reorder')->name('powerpanel.service-category.reorder');
    Route::get('powerpanel/service-category/add', 'Powerpanel\ServicesCategory\Controllers\Powerpanel\ServiceCategoryController@edit')->name('powerpanel.service-category.add');
    Route::post('powerpanel/service-category/add/', 'Powerpanel\ServicesCategory\Controllers\Powerpanel\ServiceCategoryController@handlePost')->name('powerpanel.service-category.handleAddPost');
    Route::get('powerpanel/service-category/{alias}/edit', 'Powerpanel\ServicesCategory\Controllers\Powerpanel\ServiceCategoryController@edit')->name('powerpanel.service-category.edit');
    Route::post('powerpanel/service-category/{alias}/edit', 'Powerpanel\ServicesCategory\Controllers\Powerpanel\ServiceCategoryController@handlePost')->name('powerpanel/service-category/handleEditPost');
    Route::post('powerpanel/service-category/DeleteRecord', 'Powerpanel\ServicesCategory\Controllers\Powerpanel\ServiceCategoryController@DeleteRecord');
});
