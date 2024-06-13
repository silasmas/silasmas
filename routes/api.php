<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::apiResource('message', 'App\Http\Controllers\API\MessageController');
Route::apiResource('project', 'App\Http\Controllers\API\ProjectController');
Route::apiResource('role', 'App\Http\Controllers\API\RoleController');
Route::apiResource('status', 'App\Http\Controllers\API\StatusController');
Route::apiResource('user', 'App\Http\Controllers\API\UserController');
Route::apiResource('website', 'App\Http\Controllers\API\WebsiteController');

/*
|--------------------------------------------------------------------------
| Custom API resource
|--------------------------------------------------------------------------
 */
Route::group(['middleware' => 'api'], function () {
    Route::resource('message', 'App\Http\Controllers\API\MessageController');
    Route::resource('project', 'App\Http\Controllers\API\ProjectController');
    Route::resource('role', 'App\Http\Controllers\API\RoleController');
    Route::resource('status', 'App\Http\Controllers\API\StatusController');
    Route::resource('user', 'App\Http\Controllers\API\UserController');

    // Message
    Route::get('message/search/{data}', 'App\Http\Controllers\API\MessageController@search')->name('message.api.search');
    // Project
    Route::get('project/search/{data}', 'App\Http\Controllers\API\ProjectController@search')->name('project.api.search');
    Route::get('project/find_by_status/{status_name}', 'App\Http\Controllers\API\ProjectController@findByStatus')->name('project.api.find_by_status');
    // Status
    Route::get('status/search/{data}', 'App\Http\Controllers\API\StatusController@search')->name('status.api.search');
    // Role
    Route::get('role/search/{data}', 'App\Http\Controllers\API\RoleController@search')->name('role.api.search');
    // User
    Route::get('user/search/{data}', 'App\Http\Controllers\API\UserController@search')->name('user.api.search');
    Route::get('user/find_by_role/{role_name}', 'App\Http\Controllers\API\UserController@findByRole')->name('user.api.find_by_role');
    Route::get('user/find_by_not_role/{role_name}', 'App\Http\Controllers\API\UserController@findByNotRole')->name('user.api.find_by_not_role');
    Route::get('user/find_by_status/{status_id}', 'App\Http\Controllers\API\UserController@findByStatus')->name('user.api.find_by_status');
    Route::post('user/login', 'App\Http\Controllers\API\UserController@login')->name('user.api.login');
    Route::put('user/withdraw_role/{id}', 'App\Http\Controllers\API\UserController@withdrawRole')->name('user.api.withdraw_role');
    Route::put('user/update_password/{id}', 'App\Http\Controllers\API\UserController@updatePassword')->name('user.api.update_password');
    Route::put('user/update_avatar_picture/{id}', 'App\Http\Controllers\API\UserController@updateAvatarPicture')->name('user.api.update_avatar_picture');
});