<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => ['token']], function (){
    Route::get('/user/info', [App\Http\Controllers\UsersController::class, 'accountInfo']);
    Route::get('/sim/services', [App\Http\Controllers\ServiceController::class, 'getAll']);
    Route::get('/sim/networks', [App\Http\Controllers\NetworkController::class, 'getAll']);

    Route::get('/sim/rent', [App\Http\Controllers\SimController::class, 'userRent']);

    Route::get('/updateSim', [App\Http\Controllers\SimController::class, 'updateSimClient']);
});