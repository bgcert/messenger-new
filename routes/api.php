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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/threads', 'MessengerController@threads');
Route::get('/thread/{id}', 'MessengerController@thread');
Route::get('/thread/{id}/messages', 'MessengerController@messages');
Route::post('/thread', 'MessengerController@newThread'); // For later
Route::post('/thread/{id}/message/add', 'MessengerController@addMessage');
Route::post('/thread/message/new', 'MessengerController@newMessage');
Route::post('/thread/seen', 'MessengerController@seen');
Route::post('/search', 'MessengerController@search');