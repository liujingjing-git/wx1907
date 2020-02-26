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
    // phpinfo();die;
    return view('welcome');
});

/*验签测试*/
Route::prefix('/test')->group(function(){
    Route::get('/sign','TestController@sign');
    Route::get('/encrypt','TestController@encrypt'); //加密
    Route::get('/decrypt','TestController@decrypt');    //解密
    Route::get('/encrypt1','TestController@encrypt1'); 
    Route::get('/yan','TestController@yan'); 
    Route::get('/Asymmetric','TestController@Asymmetric');  //非对称加密
    Route::get('/rsaSign','TestController@rsaSign');//非对称加密 验签
});
