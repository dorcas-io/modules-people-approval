<?php
Route::group(['namespace' => 'Dorcas\ModulesPeopleApproval\Http\Controllers', 'middleware' => ['web', 'auth'], 'prefix' => 'mpe'], function () {
    Route::get('approval-search', 'ModulesPeopleApprovalController@searchApproval')->name('approval-search');
    Route::get('approval-main', 'ModulesPeopleApprovalController@index')->name('approval-main');
    Route::post('approval', 'ModulesPeopleApprovalController@createApproval')->name('approval-create');
    Route::delete('approval/{id}', 'ModulesPeopleApprovalController@deleteApproval')->name('approval');
    Route::get('approval/single/{id}', 'ModulesPeopleApprovalController@approvalSingle')->name('approval');
    Route::put('approval/{id}', 'ModulesPeopleApprovalController@updateApproval')->name('approval');

    Route::get('approval/authorizers/{id}', 'ModulesPeopleApprovalController@approvalAuthorizer')->name('approval-authorizers');
    Route::get('authorizer-search/{id}', 'ModulesPeopleApprovalController@searchAuthorizer')->name('authorizer-search');
     Route::post('approval/authorizer/create', 'ModulesPeopleApprovalController@createAuthorizer')->name('authorizer-create');
    Route::post('approval/authorizer', 'ModulesPeopleApprovalController@deleteAuthorizer')->name('authorizer-delete');

    Route::get('approval/request/{id}','ModulesPeopleApprovalController@approvalRequestForm')->name('view-request');
    Route::post('approval/requests','ModulesPeopleApprovalController@requestAction')->name('request-action');


});