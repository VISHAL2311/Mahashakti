<?php

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('powerpanel/workflow/', 'Powerpanel\Workflow\Controllers\Powerpanel\WorkflowController@index')->name('powerpanel.workflow.list');
    Route::get('powerpanel/workflow/', 'Powerpanel\Workflow\Controllers\Powerpanel\WorkflowController@index')->name('powerpanel.workflow.index');

    Route::post('powerpanel/workflow/get-admin', ['uses' => 'Powerpanel\Workflow\Controllers\Powerpanel\WorkflowController@getAdmins']);
    Route::post('powerpanel/workflow/get-admin-users', ['uses' => 'Powerpanel\Workflow\Controllers\Powerpanel\WorkflowController@getAdminUsers']);

    Route::post('powerpanel/workflow/get-category', ['uses' => 'Powerpanel\Workflow\Controllers\Powerpanel\WorkflowController@getCategory']);
    Route::post('powerpanel/workflow/get-modulebycategory', ['uses' => 'Powerpanel\Workflow\Controllers\Powerpanel\WorkflowController@getCategoryWiseModules']);
    Route::post('powerpanel/workflow/get-module-by-role', ['uses' => 'Powerpanel\Workflow\Controllers\Powerpanel\WorkflowController@getModulesByRole']);
    Route::post('powerpanel/workflow/check-wfexists', ['uses' => 'Powerpanel\Workflow\Controllers\Powerpanel\WorkflowController@wfExists']);
    Route::post('powerpanel/workflow/getChildData', ['uses' => 'Powerpanel\Workflow\Controllers\Powerpanel\WorkflowController@getChildData']);
    Route::post('powerpanel/workflow/get_list/', 'Powerpanel\Workflow\Controllers\Powerpanel\WorkflowController@get_list')->name('powerpanel.workflow.get_list');

    Route::post('powerpanel/workflow/DeleteRecord', 'Powerpanel\Workflow\Controllers\Powerpanel\WorkflowController@DeleteRecord');
    Route::post('powerpanel/workflow/publish', ['uses' => 'Powerpanel\Workflow\Controllers\Powerpanel\WorkflowController@publish', 'middleware' => 'permission:' . Config::get('Constant.MODULE.NAME') . '-edit']);
    Route::post('powerpanel/workflow/reorder', ['as' => Config::get('Constant.MODULE.NAME') . '.reorder', 'uses' => 'Powerpanel\Workflow\Controllers\Powerpanel\WorkflowController@reorder', 'middleware' => 'permission:' . Config::get('Constant.MODULE.NAME') . '-list']);
    Route::post('powerpanel/workflow/insertComents', ['as' => 'pages.index', 'uses' => 'Powerpanel\Workflow\Controllers\Powerpanel\WorkflowController@insertComents']);
});
