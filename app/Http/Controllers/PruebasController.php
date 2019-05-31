<?php

namespace App\Http\Controllers;

USE App\Post;
use App\Category;


//use Illuminate\Http\Request;

class PruebasController extends Controller
{
    //creamos un metodo

    public function index()
    {
        $animales = ['perro', 'gato', 'tigre'];
        $titulo = 'Animales';

        // pruebas index hace referencia a carpeta.archivo
        return view('pruebas.index', [
            'animales' => $animales,
            'titulo' => $titulo
        ]);
    }


    public function testorm()
    {
        /*$posts = Post::all();
        foreach ($posts as $post) {
            echo "<h1>$post->title</h1>";
            echo "<span>{$post->user->name} - {$post->category->name}</span>";
            echo "<p>$post->content</p><hr/>";
        }*/

        $categories = Category::all();
        foreach ($categories as $category) {

            echo "<h1>$category->name</h1>";

            foreach ($category->posts as $post) {

                echo "<h3>$post->title</h3>";
                echo "<span>{$post->user->name} - {$post->category->name}</span>";
                echo "<p>$post->content</p>";

            }
            echo "<hr/>";
        }


        die;
    }
}


