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
Route::post('/api/auth','ApiController@auth');
Route::post('/test/post','TestController@check2');//post验签
Route::get('/test/get','TestController@md5test');//get验签
Route::get('/test/sign3Md5','TestController@sign3Md5');//非对称数据加密

