<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {

        // por dfecto devolvemos error generico
        $data = [
            'status'  => 'error',
            'code'    => 400,
            'message' => 'Envia los datos correctamente',
        ];

        // recoger datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (empty($params_array)) return response()->json($data, $data['code']);

        // conseguir usuario identificado
        $jwAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwAuth->checkToken($token, true);

        // validar datos
        $validate = \Validator::make($params_array, [
            'title'       => 'required',
            'content'     => 'required',
            'category_id' => 'required',
            'image'       => 'required'
        ]);

        if ($validate->fails()) {
            $data['message'] = 'Faltan datos';
            $data['errors'] = $validate->errors();
            return response()->json($data, $data['code']);
        }

        // guardar post
        $post = new Post();
        $post->user_id = $user->sub;
        $post->category_id = $params_array['category_id'];
        $post->title = $params_array['title'];
        $post->content = $params_array['content'];
        $post->image = $params_array['image'];
        $post->save();

        // devolver resultado
        $data = [
            'status' => 'success',
            'code'   => 200,
            'post'   => $post,
        ];

        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request) {

        // por dfecto devolvemos error generico
        $data = [
            'status'  => 'error',
            'code'    => 400,
            'message' => 'Envia los datos correctamente',
        ];

        // recoger datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (empty($params_array)) return response()->json($data, $data['code']);

        // validar datos
        $validate = \Validator::make($params_array, [
            'title'       => 'required',
            'content'     => 'required',
            'category_id' => 'required',
            'image'       => 'required'
        ]);

        if ($validate->fails()) {
            $data['message'] = 'Faltan datos';
            $data['errors'] = $validate->errors();
            return response()->json($data, $data['code']);
        }

        // quitar lo que no queremos actualizar
        unset($params_array['id']);
        unset($params_array['user_id']);
        unset($params_array['created_at']);

        // actualizar el registro
        $POST = Post::where('id', $id)->update($params_array);

        $data = [
            'status' => 'success',
            'code'   => 200,
            'POST'   => $params_array,
        ];

        // devolver resultado
        return response()->json($data, $data['code']);
    }
}
