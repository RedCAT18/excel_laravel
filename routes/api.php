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

Route::middleware('auth:api')->post('/user', 'UserController@index');

Route::get('/menu', 'ExcelController@loadMenu');
Route::post('/excel', 'ExcelController@loadExcel');
Route::get('/users', 'ExcelController@loadUser');
