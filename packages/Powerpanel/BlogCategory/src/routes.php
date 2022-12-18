<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/blog-category', 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@index')->name('powerpanel.blog-category.index');
    Route::post('powerpanel/blog-category/get_list/', 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@get_list');
    Route::post('powerpanel/blog-category/get_list_New/', 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@get_list_New');
    Route::post('powerpanel/blog-category/get_list_favorite/', 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@get_list_favorite');
    Route::post('powerpanel/blog-category/get_list_draft/', 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@get_list_draft');
    Route::post('powerpanel/blog-category/get_list_trash/', 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@get_list_trash');

    Route::post('powerpanel/blog-category/publish', ['uses' => 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@publish', 'middleware' => 'permission:' . Config::get('Constant.MODULE.NAME') . '-edit']);

    Route::get('powerpanel/blog-category/reorder/', 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@reorder')->name('powerpanel.blog-category.reorder');

    Route::post('powerpanel/blog-category/addpreview/', 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@addPreview')->name('powerpanel.blog-category.addpreview');

    Route::get('powerpanel/blog-category/add', 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@edit')->name('powerpanel.blog-category.add');
    Route::post('powerpanel/blog-category/add/', 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@handlePost')->name('powerpanel.blog-category.handleAddPost');
    
    Route::get('powerpanel/blog-category/{alias}/edit', 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@edit')->name('powerpanel.blog-category.edit');
    Route::post('powerpanel/blog-category/{alias}/edit', 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@handlePost')->name('powerpanel/blog-category/handleEditPost');

    Route::post('powerpanel/blog-category/DeleteRecord', 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@DeleteRecord');

    Route::post('powerpanel/blog-category/getChildData', ['uses' => 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@getChildData', 'middleware' => 'permission:blog-category-list']);
    Route::post('powerpanel/blog-category/ApprovedData_Listing', ['uses' => 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@ApprovedData_Listing', 'middleware' => 'permission:blog-category-list']);
    Route::post('powerpanel/blog-category/getChildData_rollback', ['uses' => 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@getChildData_rollback']);

    Route::post('powerpanel/blog-category/rollback-record', ['uses' => 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@rollBackRecord', 'middleware' => 'permission:blog-category-list']);
    
    Route::post('powerpanel/blog-category/Get_Comments', ['uses' => 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@Get_Comments']);
    Route::post('powerpanel/blog-category/get_builder_list', ['uses' => 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@get_builder_list']);
    Route::post('powerpanel/blog-category/getAllCategory', ['uses' => 'Powerpanel\BlogCategory\Controllers\Powerpanel\BlogCategoryController@getAllCategory']);
});
