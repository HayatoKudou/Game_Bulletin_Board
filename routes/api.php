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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('test', 'App\Http\Controllers\Api\AuthController@test');
Route::post('playersProfileStats', 'App\Http\Controllers\Api\ApexController@playersProfileStats');
Route::post('playerStatistics', 'App\Http\Controllers\Api\ApexController@playerStatistics');
Route::post('searchApexPlayer', 'App\Http\Controllers\Api\ApexController@searchApexPlayer');