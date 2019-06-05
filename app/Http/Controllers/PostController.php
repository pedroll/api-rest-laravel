<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{

    /**
     * CategoryController constructor.
     * Cargamos el middleware auth excepto para metodos...
     */
    public function __construct() {
        $this->middleware('\App\Http\Middleware\ApiAuthMiddleware', ['except' => ['index', 'show']]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        // con el load tambien debuelve la cetgoria
        $posts = Post::all()->load('category');
        $data = [
            'status' => 'success',
            'code'   => 200,
            'posts'  => $posts,
        ];
        return response()->json($data, $data['code']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {
        $post = Post::find($id)->load('category');
        if (is_object($post)) {
            $data = [
                'status' => 'success',
                'code'   => 200,
                'post'   => $post,
            ];
        } else {
            $data = [
                'status'  => 'error',
                'code'    => 404,
                'message' => 'La entrada no existe',
            ];
        }
        return response()->json($data, $data['code']);

    }

}
