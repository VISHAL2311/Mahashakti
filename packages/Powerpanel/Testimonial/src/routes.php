<?php
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('powerpanel/testimonial', 'Powerpanel\Testimonial\Controllers\Powerpanel\TestimonialController@index')->name('powerpanel.testimonial.index');
    Route::post('powerpanel/testimonial/get_list/', 'Powerpanel\Testimonial\Controllers\Powerpanel\TestimonialController@get_list');
    Route::post('powerpanel/testimonial/publish', ['uses' => 'Powerpanel\Testimonial\Controllers\Powerpanel\TestimonialController@publish', 'middleware' => 'permission:' . Config::get('Constant.MODULE.NAME') . '-edit']);
    Route::get('powerpanel/testimonial/reorder/', 'Powerpanel\Testimonial\Controllers\Powerpanel\TestimonialController@reorder')->name('powerpanel.testimonial.reorder');
    Route::get('powerpanel/testimonial/add', 'Powerpanel\Testimonial\Controllers\Powerpanel\TestimonialController@edit')->name('powerpanel.testimonial.add');
    Route::post('powerpanel/testimonial/add/', 'Powerpanel\Testimonial\Controllers\Powerpanel\TestimonialController@handlePost')->name('powerpanel.testimonial.handleAddPost');
    Route::get('powerpanel/testimonial/{alias}/edit', 'Powerpanel\Testimonial\Controllers\Powerpanel\TestimonialController@edit')->name('powerpanel.testimonial.edit');
    Route::post('powerpanel/testimonial/{alias}/edit', 'Powerpanel\Testimonial\Controllers\Powerpanel\TestimonialController@handlePost')->name('powerpanel/testimonial/handleEditPost');
    Route::post('powerpanel/testimonial/DeleteRecord', 'Powerpanel\Testimonial\Controllers\Powerpanel\TestimonialController@DeleteRecord');
    Route::post('powerpanel/testimonial/get_builder_list', ['uses' => 'Powerpanel\Testimonial\Controllers\Powerpanel\TestimonialController@get_builder_list']);
});
