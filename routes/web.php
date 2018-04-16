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


Route::get('/', 'Check\CheckController@index');
Route::post('/', 'Check\CheckController@check');











Route::get('save', 'Check\CheckController@save');
Route::get('results', 'Check\CheckController@results');
// Route::post('save', 'Check\CheckController@save');
