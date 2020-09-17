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

//Auth
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signUp');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});

//User
Route::group([
    'prefix' => 'user'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('all', 'UserController@users');
        Route::get('shops', 'UserController@shops');
        Route::get('{id}', 'UserController@show');
        Route::put('{id}', 'UserController@edit');
        Route::delete('{id}', 'UserController@delete');
    });
});

//Service
Route::group([
    'prefix' => 'service'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
    	Route::post('', 'ServiceController@create')->middleware('shop');
        Route::get('mine', 'ServiceController@getMine')->middleware('shop');
        Route::get('', 'ServiceController@get');
        Route::get('{id}', 'ServiceController@show');
        Route::put('{id}', 'ServiceController@edit')->middleware('shop');
        Route::delete('{id}', 'ServiceController@delete')->middleware('shop');
    });
});

//Service
Route::group([
    'prefix' => 'permission'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
    	Route::post('{id}', 'ServiceController@createPermission')->middleware('shop');
        Route::put('{id}', 'ServiceController@editPermission')->middleware('shop');
        Route::delete('{id}', 'ServiceController@deletePermission')->middleware('shop');
    });
});