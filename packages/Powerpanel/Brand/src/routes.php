<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/brand/', 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@index')->name('powerpanel.brand.list');
    Route::get('powerpanel/brand/', 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@index')->name('powerpanel.brand.index');
    
    Route::post('powerpanel/brand/get_list/', 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@get_list')->name('powerpanel.brand.get_list');
    Route::post('powerpanel/brand/get_list_New/', 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@get_list_New')->name('powerpanel.brand.get_list_New');
    Route::post('powerpanel/brand/get_list_draft/', 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@get_list_draft')->name('powerpanel.brand.get_list_draft');
    Route::post('powerpanel/brand/get_list_trash/', 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@get_list_trash')->name('powerpanel.brand.get_list_trash');
    Route::post('powerpanel/brand/get_list_favorite/', 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@get_list_favorite')->name('powerpanel.brand.get_list_favorite');

    Route::get('powerpanel/brand/add/', 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@edit')->name('powerpanel.brand.add');
    Route::post('powerpanel/brand/add/', 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@handlePost')->name('powerpanel.brand.add');

    Route::post('powerpanel/brand/DeleteRecord', 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@DeleteRecord');
    Route::post('powerpanel/brand/publish', ['uses' => 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@publish', 'middleware' => 'permission:brand-edit']);
    Route::post('powerpanel/brand/reorder', ['as' => Config::get('Constant.MODULE.NAME') . '.reorder', 'uses' => 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@reorder', 'middleware' => 'permission:brand-list']);
       
    Route::post('powerpanel/brand/getChildData', ['uses' => 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@getChildData', 'middleware' => 'permission:brand-list']);
    Route::post('powerpanel/brand/ApprovedData_Listing', ['uses' => 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@ApprovedData_Listing', 'middleware' => 'permission:brand-list']);
    Route::post('powerpanel/brand/getChildData_rollback', ['uses' => 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@getChildData_rollback']);
    Route::post('powerpanel/brand/insertComents', ['uses' => 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@insertComents']);
    Route::post('powerpanel/brand/Get_Comments', ['uses' => 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@Get_Comments']);
    Route::post('powerpanel/brand/get_builder_list', 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@get_buider_list');
    Route::post('/powerpanel/brand/addpreview', ['as' => 'powerpanel.brand.addpreview', 'uses' => 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@addPreview', 'middleware' => 'permission:brand-create']);
    Route::post('powerpanel/brand/rollback-record', ['uses' => 'Powerpanel\Brand\Controllers\Powerpanel\BrandController@rollBackRecord', 'middleware' => 'permission:brand-list']);
});
