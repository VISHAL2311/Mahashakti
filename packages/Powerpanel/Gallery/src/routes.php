<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    
    Route::post('powerpanel/gallery/', 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@handlePost')->name('powerpanel.gallery.list');
    Route::post('powerpanel/gallery/update', 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@update_store')->name('powerpanel.gallery.list');
    
    Route::get('powerpanel/gallery/', 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@index')->name('powerpanel.gallery.list');
    Route::get('powerpanel/gallery/', 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@index')->name('powerpanel.gallery.index');
    
    Route::post('powerpanel/gallery/get_list/', 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@get_list')->name('powerpanel.gallery.get_list');
    Route::post('powerpanel/gallery/get_list_New/', 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@get_list_New')->name('powerpanel.gallery.get_list_New');
    Route::post('powerpanel/gallery/get_list_draft/', 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@get_list_draft')->name('powerpanel.gallery.get_list_draft');
    Route::post('powerpanel/gallery/get_list_trash/', 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@get_list_trash')->name('powerpanel.gallery.get_list_trash');
    Route::post('powerpanel/gallery/get_list_favorite/', 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@get_list_favorite')->name('powerpanel.gallery.get_list_favorite');

//    Route::get('powerpanel/gallery/add/', 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@edit')->name('powerpanel.gallery.add');
//    Route::post('powerpanel/gallery/add/', 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@handlePost')->name('powerpanel.gallery.add');

    Route::post('powerpanel/gallery/DeleteRecord', 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@DeleteRecord');
    Route::post('powerpanel/gallery/update_status', ['uses' => 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@update_status']);
    Route::post('powerpanel/gallery/reorder', ['as' => Config::get('Constant.MODULE.NAME') . '.reorder', 'uses' => 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@reorder', 'middleware' => 'permission:gallery-list']);
       
    Route::post('powerpanel/gallery/getChildData', ['uses' => 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@getChildData', 'middleware' => 'permission:gallery-list']);
    Route::post('powerpanel/gallery/ApprovedData_Listing', ['uses' => 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@ApprovedData_Listing', 'middleware' => 'permission:gallery-list']);
    Route::post('powerpanel/gallery/getChildData_rollback', ['uses' => 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@getChildData_rollback']);
    Route::post('powerpanel/gallery/insertComents', ['uses' => 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@insertComents']);
    Route::post('powerpanel/gallery/Get_Comments', ['uses' => 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@Get_Comments']);
    Route::post('powerpanel/gallery/get_builder_list', 'Powerpanel\Gallery\Controllers\Powerpanel\GalleryController@get_buider_list');
});
