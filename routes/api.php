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
Route::post('password/reset', 'App\Http\Controllers\Api\AuthController@sendResetLinkEmail');

Route::post('apex_post', 'App\Http\Controllers\Api\ApexController@post');
Route::post('apex_delete_article', 'App\Http\Controllers\Api\ApexController@delete_article');
Route::post('apex_get_articles', 'App\Http\Controllers\Api\ApexController@get_articles');

Route::post('get_notice', 'App\Http\Controllers\Api\ApexController@get_notice');
Route::post('clear_notice', 'App\Http\Controllers\Api\ApexController@clear_notice');