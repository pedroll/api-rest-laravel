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

// RUTAS DE PRUEB
//en lugar del view podemos pasar el controlador
Route::get('/animales', 'PruebasController@index');
Route::get('/test-orm', 'PruebasController@testorm');


// Rutas del api

// RUTAS DE PRUEB
Route::get('usuario/pruebas', 'UsersController@pruebas');
Route::get('categoria/pruebas', 'categoryController@pruebas');
Route::get('entrada/pruebas', 'postController@pruebas');

/*
 * Metodos http comunes
 * */
// Buenas
Route::post('/api/register', 'UsersController@register');
Route::post('/api/login', 'UsersController@login');
Route::put('/api/user/update', 'UsersController@update')->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
// llamamos al middleeware creado para la ruta
Route::post('/api/user/upload', 'UsersController@upload')->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::get('/api/user/avatar/{filename}', 'UsersController@getImage');
Route::get('/api/user/detail/{id}', 'UsersController@detail');
