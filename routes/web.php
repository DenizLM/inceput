<?php

use App\Http\Controllers\OpenDataController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoutesController;
use App\Http\Controllers\StationsController;
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

Route::get('/', [MenuController::class, 'index']);

Route::get('/map', [MapController::class, 'index']);

Route::get('/stations', [StationsController::class, 'index']);

Route::get('/routes', [RoutesController::class, 'index']);

Route::get('/get-data', [HomeController::class, 'getData']);

Route::get('/get-coordinates', [OpenDataController::class, 'getVehiclesPositions'])->name('get-coordinates');

Route::get('/get-stops', [OpenDataController::class, 'getStops'])->name('get-stops');

Route::get('get-route/{route}', [OpenDataController::class, 'getRoute'])->name('get-route');
