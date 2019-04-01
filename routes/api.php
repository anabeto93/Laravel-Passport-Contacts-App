<?php

use Illuminate\Http\Request;

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

/*Route::middleware('auth:api')->post('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['prefix' => 'v1'], function() {
    Route::group(['middleware' => 'auth:api'], function() {
        Route::post('user', function(Request $request) {
            return $request->user();
        });
        Route::apiResource('contacts','Api\ContactsController');
    });

    //UNSECURE ENDPOINTS
    Route::post('register','Api\AuthController@register');
    Route::post('login','Api\AuthController@login');

    //Route::apiResource('contacts','Api\ContactsController');
});
