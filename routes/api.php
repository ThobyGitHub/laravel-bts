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

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'Auth\RegisterController@create');

Route::group([

    'middleware' => 'api',

], function ($router) {
    
    Route::get('checklist/{checklist_id}/item/{item_id}', 'ChecklistController@getItemByChecklistId');
    Route::get('checklist', 'ChecklistController@index');
    Route::post('checklist', 'ChecklistController@store');
    Route::put('checklist/{checklist_id}/item/{item_id}', 'ChecklistController@update');
    Route::delete('checklist/{checklist_id}/item/{item_id}', 'ChecklistController@destroy');
    Route::put('checklist/{checklist_id}/item/rename/{item_id}', 'ChecklistController@rename');
});