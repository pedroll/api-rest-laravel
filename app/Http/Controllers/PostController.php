<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Post;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/**
 * Class PostController
 * @package App\Http\Controllers
 */
class PostController extends Controller
{

    /**
     * CategoryController constructor.
     * Cargamos el middleware auth excepto para metodos...
     */
    public function __construct() {
        $this->middleware('api.auth', ['except' => [
            'index',
            'show',
            'getImage',
            'getPostsByCategory',
            'getPostsByUser'
        ]]);
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
        $user = $this->getUser($request);

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

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
        // conseguir usuario identificado
        $user = $this->getUser($request);
//        if ($user->sub != $params_array['user_id']) {
//            $data['message'] = 'Solo puedes actualizar post propios';
//
//            return response()->json($data, $data['code']);
//        }

        // quitar lo que no queremos actualizar
        unset($params_array['id']);
        unset($params_array['user_id']);
        unset($params_array['created_at']);
        unset($params_array['user']);

        // actualizar el registro
        // al actualizar con el metodo updateOrCreate devuelbe el objeto en si
        // upxate or create esta limitado a un filtro where se lo introducimod en un array
        $where = [
            'id'      => $id,
            'user_id' => $user->sub
        ];
        /* $post = Post::where($where)->updateOrCreate($params_array);*/
        $post = Post::where($where)->first();
        if (empty($post) || !is_object($post)) return response()->json($data, $data['code']);

        $post->update($params_array);

        $data = [
            'status'  => 'success',
            'code'    => 200,
            'changes' => $params_array,
            'post'    => $post
        ];

        // devolver resultado
        return response()->json($data, $data['code']);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id, Request $request) {

        $post = null;

        // por dfecto devolvemos error generico
        $data = [
            'status'  => 'error',
            'code'    => 400,
            'message' => 'No se ha encontado post para borrar',
            'post'    => $post
        ];

        // conseguir usuario identificado
        $user = $this->getUser($request);

        // conseguir registro solo para usuario autentificado
        //$post = Post::find($id);
        $post = Post::where('id', $id)
            ->where('user_id', $user->sub)
            ->first();

        if (!$post) return response()->json($data, $data['code']);

        $data['message'] = ' solo puedes borrar tus posts';
        if (!isset($post->id)) return response()->json($data, $data['code']);


        // Borrarlo
        $post->delete();
        // devolver algo
        $data = [
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Borrado corectamente',
            'post'    => $post
        ];
        return response()->json($data, $data['code']);

    }

    /**
     * @param Request $request
     * @return bool|object|null
     */
    private function getUser(Request $request) {
        $jwAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwAuth->checkToken($token, true);

        return $user;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request) {
        $data = array(
            'status'  => 'error',
            'code'    => '400',
            'message' => 'Error al subir la imagen',

        );
        // recoger datos peticionm
        $image = $request->file('file0');

        // validacion imagen

        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ], ['Tiene que ser una imagen valida']);

        // guardar imagen
        if (!$image || $validate->fails()) {
            $data['errors'] = $validate->errors()->all();
            return response()->json($data, $data['code']);
        }

        $image_name = time() . $image->getClientOriginalName();
        // discos virtuales
        try {
            \Storage::disk('images')->put($image_name, \File::get($image));
        } catch (FileNotFoundException $e) {
            $data['errors'] = $e->getMessage();
            return response()->json($data, $data['code']);
        }

        $data = array(
            'status'  => 'success',
            'code'    => '200',
            'image'   => $image_name,
            'message' => 'Imagen subida',
        );

        return response()->json($data, $data['code']);

    }


    /**
     * @param $filename
     * @return Response
     */
    public function getImage($filename) {
        $isset = \Storage::disk('images')->exists($filename);
        try {
            $file = \Storage::disk('images')->get($filename);
        } catch (FileNotFoundException $e) {
            $data = array(
                'status' => 'error',
                'code'   => 400,
                'errors' => $e->getMessage(),
            );
            if (!$isset) $data['message'] = 'imagen no existe';

            return response()->json($data, $data['code']);
        }
        // da erro al devolver objeto

        return new Response($file, 200);
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPostsByCategory($id) {

        $posts = Post::where('category_id', $id)->get();


        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'posts'  => $posts,
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPostsByUser($id) {
        $posts = Post::where('user_id', $id)->get();


        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'posts'  => $posts,
        ], 200);
    }
}
