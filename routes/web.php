<?php

use App\Http\Controllers\Install\InstallController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/mohosin', function () {
    var_dump("Hello Bangladesh");
    die;
});

Route::get('/', function () {
    var_dump("Hello MOHOSIN");
    die("I am Here");
    return view('welcome');
});

Route::get('install', [InstallController::class, 'index']);
Route::group(['prefix' => 'install'], function () {
    Route::get('start', [InstallController::class, 'index']);
    Route::get('requirements', [InstallController::class, 'requirements']);
    Route::get('permissions', [InstallController::class, 'permissions']);
    Route::any('database', [InstallController::class, 'database']);
    Route::any('installation', [InstallController::class, 'installation']);
    Route::get('complete', [InstallController::class, 'complete']);
});
