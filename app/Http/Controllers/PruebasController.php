<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PruebasController extends Controller
{
    //creamos un metodo

    public  function index() {
        $animales =['perro', 'gato', 'tigre'];
$titulo ='Animales';
        // pruebas index hace referencia a carpeta.archivo
        return view('pruebas.index', [
            'animales' => $animales,
            'titulo' => $titulo
        ]);
    }
}
