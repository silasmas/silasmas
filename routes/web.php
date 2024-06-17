<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
 */
// Public
Route::get('/', 'App\Http\Controllers\Web\HomeController@index')->name('home');
Route::get('/symlink', 'App\Http\Controllers\Web\HomeController@symlink')->name('symlink');
Route::get('/detail/{id}', 'App\Http\Controllers\Web\HomeController@detail')->name('detail');
Route::post('addNewMessage', [AdminController::class, 'sendMessage'])->name('addNewMessage');

// Administration
Route::middleware('auth')->group(function () {
    Route::get('/admin', 'App\Http\Controllers\Web\HomeController@index')->name('admin.home');
    Route::get('/admin', 'App\Http\Controllers\Web\AdminController@index')->name('admin.home');

    Route::get('/account', 'App\Http\Controllers\Web\AdminController@account')->name('admin.account');
    Route::post('/account', 'App\Http\Controllers\Web\AdminController@updateAccount');

    Route::get('/message', 'App\Http\Controllers\Web\AdminController@message')->name('admin.message');

    Route::get('/project', 'App\Http\Controllers\Web\AdminController@project')->name('admin.project');
    Route::post('/project', 'App\Http\Controllers\Web\AdminController@addProject');

    Route::get('/team', 'App\Http\Controllers\Web\AdminController@team')->name('admin.team');
    Route::post('/team', 'App\Http\Controllers\Web\AdminController@addMember');
});

require __DIR__ . '/auth.php';
