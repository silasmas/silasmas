<?php

use App\Http\Controllers\API\Academy\AcademyController;
use App\Http\Controllers\API\Academy\AcademyParticipantController;
use App\Http\Controllers\API\Academy\AcademyPaymentController;
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
| SDev Academy (API publique pour le front Next.js)
|--------------------------------------------------------------------------
 */
Route::prefix('academy')->group(function () {
    Route::get('sessions', [AcademyController::class, 'sessions'])->name('academy.sessions.index');
    Route::get('sessions/{slug}', [AcademyController::class, 'showSession'])->name('academy.sessions.show');
    Route::post('register', [AcademyController::class, 'register'])->name('academy.register');
    Route::post('payments/process', [AcademyPaymentController::class, 'processPayment'])
      ->name('academy.payments.process');
    Route::get('payments/check-status', [AcademyPaymentController::class, 'checkTransactionStatus'])
      ->name('academy.payments.check');
    Route::post('payments/confirm', [AcademyPaymentController::class, 'confirmPayment'])
      ->name('academy.payments.confirm');
    Route::get('participant/{token}', [AcademyParticipantController::class, 'show'])
      ->name('academy.participant.show');
    Route::post('participant/{token}/accept-confidentiality', [AcademyParticipantController::class, 'acceptConfidentiality'])
      ->name('academy.participant.accept-confidentiality');
});

Route::post('contact', [\App\Http\Controllers\API\ContactController::class, 'store'])->name('contact.store');

Route::get('site', [\App\Http\Controllers\API\SiteController::class, 'index'])->name('site.index');

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