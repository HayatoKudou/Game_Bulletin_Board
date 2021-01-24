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

Route::post('login', 'App\Http\Controllers\Api\AuthController@login');
Route::post('register', 'App\Http\Controllers\Api\AuthController@register');
Route::post('apex_post', 'App\Http\Controllers\Api\ApexController@post');
Route::post('apex_get_articles', 'App\Http\Controllers\Api\ApexController@get_articles');

Route::post('playersProfileStats', 'App\Http\Controllers\Api\ApexController@playersProfileStats');
Route::post('playerStatistics', 'App\Http\Controllers\Api\ApexController@playerStatistics');
Route::post('searchApexPlayer', 'App\Http\Controllers\Api\ApexController@searchApexPlayer');