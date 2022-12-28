<?php

use App\Http\Controllers\CoordinatesController;
use App\Http\Controllers\HomeController;
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

Route::get('/', [HomeController::class, 'index']);

Route::get('/get-data', [HomeController::class, 'getData']);

Route::get('/get-coordinates', [CoordinatesController::class, 'index'])->name('get-coordinates');

Route::get('get-route/{route}', [CoordinatesController::class, 'getRoute'])->name('get-route');
