<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/work', 'Powerpanel\Work\Controllers\Powerpanel\WorkController@index')->name('powerpanel.work.index');
    Route::post('powerpanel/work/get_list/', 'Powerpanel\Work\Controllers\Powerpanel\WorkController@get_list');
    Route::post('powerpanel/work/publish', ['uses' => 'Powerpanel\Work\Controllers\Powerpanel\WorkController@publish', 'middleware' => 'permission:' . Config::get('Constant.MODULE.NAME') . '-edit']);
    Route::get('powerpanel/work/reorder/', 'Powerpanel\Work\Controllers\Powerpanel\WorkController@reorder')->name('powerpanel.work.reorder');
    Route::get('powerpanel/work/add', 'Powerpanel\Work\Controllers\Powerpanel\WorkController@edit')->name('powerpanel.work.add');
    Route::post('powerpanel/work/add/', 'Powerpanel\Work\Controllers\Powerpanel\WorkController@handlePost')->name('powerpanel.work.handleAddPost');
    Route::get('powerpanel/work/{alias}/edit', 'Powerpanel\Work\Controllers\Powerpanel\WorkController@edit')->name('powerpanel.work.edit');
    Route::post('powerpanel/work/{alias}/edit', 'Powerpanel\Work\Controllers\Powerpanel\WorkController@handlePost')->name('powerpanel/work/handleEditPost');
    Route::post('powerpanel/work/DeleteRecord', 'Powerpanel\Work\Controllers\Powerpanel\WorkController@DeleteRecord');
    Route::post('powerpanel/work/get_builder_list', ['uses' => 'Powerpanel\Work\Controllers\Powerpanel\WorkController@get_builder_list']);
    Route::post('powerpanel/work-category/get_builder_list', ['uses' => 'Powerpanel\WorkCategory\Controllers\Powerpanel\WorkCategoryController@get_builder_list']);
});
