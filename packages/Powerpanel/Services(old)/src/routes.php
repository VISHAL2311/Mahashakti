<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/services', 'Powerpanel\Services\Controllers\Powerpanel\ServicesController@index')->name('powerpanel.services.index');
    Route::post('powerpanel/services/get_list/', 'Powerpanel\Services\Controllers\Powerpanel\ServicesController@get_list');
    Route::post('powerpanel/services/publish', ['uses' => 'Powerpanel\Services\Controllers\Powerpanel\ServicesController@publish', 'middleware' => 'permission:' . Config::get('Constant.MODULE.NAME') . '-edit']);
    Route::get('powerpanel/services/reorder/', 'Powerpanel\Services\Controllers\Powerpanel\ServicesController@reorder')->name('powerpanel.services.reorder');
    Route::get('powerpanel/services/add', 'Powerpanel\Services\Controllers\Powerpanel\ServicesController@edit')->name('powerpanel.services.add');
    Route::post('powerpanel/services/add/', 'Powerpanel\Services\Controllers\Powerpanel\ServicesController@handlePost')->name('powerpanel.services.handleAddPost');
    Route::get('powerpanel/services/{alias}/edit', 'Powerpanel\Services\Controllers\Powerpanel\ServicesController@edit')->name('powerpanel.services.edit');
    Route::post('powerpanel/services/{alias}/edit', 'Powerpanel\Services\Controllers\Powerpanel\ServicesController@handlePost')->name('powerpanel/services/handleEditPost');
    Route::post('powerpanel/services/DeleteRecord', 'Powerpanel\Services\Controllers\Powerpanel\ServicesController@DeleteRecord');
    Route::post('powerpanel/services/get_builder_list', ['uses' => 'Powerpanel\Services\Controllers\Powerpanel\ServicesController@get_builder_list']);
    Route::post('powerpanel/service-category/get_builder_list', ['uses' => 'Powerpanel\ServicesCategory\Controllers\Powerpanel\ServiceCategoryController@get_builder_list']);
    Route::post('powerpanel/services/get_list_New', 'Powerpanel\Services\Controllers\Powerpanel\ServicesController@get_list_New');

    Route::post('powerpanel/services/getChildData', ['uses' => 'Powerpanel\Services\Controllers\Powerpanel\ServicesController@getChildData', 'middleware' => 'permission:' . Config::get('Constant.MODULE.NAME') . '-list']);
    Route::post('powerpanel/services/ApprovedData_Listing', ['uses' => 'Powerpanel\Services\Controllers\Powerpanel\ServicesController@ApprovedData_Listing', 'middleware' => 'permission:' . Config::get('Constant.MODULE.NAME') . '-list']);
    Route::post('powerpanel/services/rollback-record', ['uses' => 'Powerpanel\Services\Controllers\Powerpanel\ServicesController@rollBackRecord', 'middleware' => 'permission:' . Config::get('Constant.MODULE.NAME') . '-list']);

    Route::post('powerpanel/services/getChildData_rollback', ['uses' => 'Powerpanel\Services\Controllers\Powerpanel\ServicesController@getChildData_rollback']);
    Route::post('powerpanel/services/addpreview/', 'Powerpanel\Services\Controllers\Powerpanel\ServicesController@addPreview')->name('powerpanel.services.addpreview');

});
