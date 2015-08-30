<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//use Illuminate\Routing\Route;

Route::get('/', function () {
    return view('welcome');
});


// Authentication routes...
Route::post('auth/login', 'Auth\AuthController@postLogin');
// Registration routes...
Route::post('auth/register', 'Auth\AuthController@postRegister');


Route::group(['middleware' => 'auth.api'], function () {
    Route::resource('notes', 'NotesController');
});