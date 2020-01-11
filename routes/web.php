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

Route::get('/', function () {
    return view('welcome');
});

Route::post('reg','ApiController@reg');
Route::post('login','ApiController@login');
Route::get('info','ApiController@info');
Route::post('showTime','ApiController@showTime');

