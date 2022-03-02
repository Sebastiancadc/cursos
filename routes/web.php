<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


//Historial de proyectos creados 
Route::get('/historial_cursos', 'CursosController@index');

//Iniciar sesion con googgle
Route::get('/login/google', 'Auth\LoginController@redirectToProvider');
Route::get('/google-callback', 'Auth\LoginController@handleProviderCallback');
