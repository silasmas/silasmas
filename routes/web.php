<?php

use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\DeployController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques (site vitrine — le front React consommera surtout l'API)
|--------------------------------------------------------------------------
 */
Route::get('/', 'App\Http\Controllers\Web\HomeController@index')->name('home');
Route::get('/symlink', 'App\Http\Controllers\Web\HomeController@symlink')->name('symlink');

/*
|--------------------------------------------------------------------------
| Déploiement (Hostinger sans SSH) — protégé par DEPLOY_SECRET
|--------------------------------------------------------------------------
 */
Route::get('/deploy/migrate', [DeployController::class, 'migrate'])->name('deploy.migrate');
Route::get('/deploy/seed', [DeployController::class, 'seed'])->name('deploy.seed');
Route::get('/detail/{id}', 'App\Http\Controllers\Web\HomeController@detail')->name('detail');
Route::post('addNewMessage', [AdminController::class, 'sendMessage'])->name('addNewMessage');

/*
|--------------------------------------------------------------------------
| Administration : Filament sur /admin (voir AdminPanelProvider)
|--------------------------------------------------------------------------
 */
