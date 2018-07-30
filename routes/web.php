<?php

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

Auth::login(\App\User::find(6));

Route::get('/q', function () {
    $threads = auth()->user()->threads;
    return view('messenger', compact($threads));
});

Route::get('/', function () {
    return view('messenger');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
