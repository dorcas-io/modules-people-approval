<?php
Route::group(['namespace' => 'Dorcas\ModulesPeopleApproval\Http\Controllers', 'middleware' => ['web', 'auth'], 'prefix' => 'mpe'], function () {
    Route::get('approval-search', 'ModulesPeopleApprovalController@searchApproval')->name('approval-search');
    Route::get('approval-main', 'ModulesPeopleApprovalController@index')->name('approval-main');
    Route::post('approval', 'ModulesPeopleApprovalController@createApproval')->name('approval-create');
    Route::delete('approval/{id}', 'ModulesPeopleApprovalController@deleteApproval')->name('approval');
});