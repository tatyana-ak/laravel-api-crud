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
$apiRouter->get('/users', 'Api\UserController@index');
$apiRouter->get('/users/{id}', 'Api\UserController@show')->where(['id' => '[0-9]+']);
$apiRouter->post('/users', 'Api\UserController@store');
$apiRouter->put('/users/{id}', 'Api\UserController@update')->where(['id' => '[0-9]+']);
$apiRouter->delete('/users/{id}', 'Api\UserController@delete')->where(['id' => '[0-9]+']);
