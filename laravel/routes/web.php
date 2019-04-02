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

Route::get('/form', 'FormController@show');
Route::get('/form/list', 'FormController@index');
Route::post('/form', 'FormController@store');
Route::put('/form', 'FormController@update');
Route::delete('/form', 'FormController@delete');