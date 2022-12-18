<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/blogs', 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@index')->name('powerpanel.blogs.index');
    Route::post('powerpanel/blogs/get_list/', 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@get_list');
    Route::post('powerpanel/blogs/get_list_New/', 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@get_list_New');
    Route::post('powerpanel/blogs/get_list_favorite/', 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@get_list_favorite');
    Route::post('powerpanel/blogs/get_list_draft/', 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@get_list_draft');
    Route::post('powerpanel/blogs/get_list_trash/', 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@get_list_trash');

    Route::post('powerpanel/blogs/publish', ['uses' => 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@publish', 'middleware' => 'permission:' . Config::get('Constant.MODULE.NAME') . '-edit']);

    Route::get('powerpanel/blogs/reorder/', 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@reorder')->name('powerpanel.blogs.reorder');

    Route::post('powerpanel/blogs/addpreview/', 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@addPreview')->name('powerpanel.blogs.addpreview');

    Route::get('powerpanel/blogs/add', 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@edit')->name('powerpanel.blogs.add');
    Route::post('powerpanel/blogs/add/', 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@handlePost')->name('powerpanel.blogs.handleAddPost');
    
    Route::get('powerpanel/blogs/{alias}/edit', 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@edit')->name('powerpanel.blogs.edit');
    Route::post('powerpanel/blogs/{alias}/edit', 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@handlePost')->name('powerpanel/blogs/handleEditPost');

    Route::post('powerpanel/blogs/DeleteRecord', 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@DeleteRecord');

    Route::post('powerpanel/blogs/getChildData', ['uses' => 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@getChildData', 'middleware' => 'permission:blogs-list']);
    Route::post('powerpanel/blogs/ApprovedData_Listing', ['uses' => 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@ApprovedData_Listing', 'middleware' => 'permission:blogs-list']);
    Route::post('powerpanel/blogs/rollback-record', ['uses' => 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@rollBackRecord', 'middleware' => 'permission:blogs-list']);

    Route::post('powerpanel/blogs/getChildData_rollback', ['uses' => 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@getChildData_rollback']);
    
    Route::post('powerpanel/blogs/insertComents', ['uses' => 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@insertComents']);
    Route::post('powerpanel/blogs/Get_Comments', ['uses' => 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@Get_Comments']);
    
    Route::post('powerpanel/blogs/get_builder_list', 'Powerpanel\Blogs\Controllers\Powerpanel\BlogsController@get_buider_list');
});
