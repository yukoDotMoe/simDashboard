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
    Route::post('/user/info', [App\Http\Controllers\UsersController::class, 'accountInfo']);
    Route::post('/sim/services', [App\Http\Controllers\ServiceController::class, 'getAll']);
    Route::post('/sim/networks', [App\Http\Controllers\NetworkController::class, 'getAll']);

    Route::post('/sim/rent', [App\Http\Controllers\SimController::class, 'userRent']);
    Route::post('/sim/get', [App\Http\Controllers\SimController::class, 'fetchRequest']);
    
});
Route::get('/token/check/{token}', [App\Http\Controllers\UsersController::class, 'checkToken']);

Route::post('/updateSim', [App\Http\Controllers\SimController::class, 'updateSimClient']);