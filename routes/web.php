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


//ruta con parametro opcional
Route::get('/pruebas/{nombre?}', function ($nombre) {
     $texto = '<h1>Prueba texto desde una ruta</h1>';
     $texto .= $nombre;
    return $texto;
});

// pasando array de datos a un template
Route::get('/pruebas2/{nombre?}', function ($nombre) {
    $texto = '<h1>Prueba texto desde una ruta</h1>';
    $texto .= $nombre.'dgsdgvfds';
    return view('pruebas', array('texto' => $texto));
});

//en lugar del view podemos pasar el controlador
Route::get('/animales', 'PruebasController@index');
