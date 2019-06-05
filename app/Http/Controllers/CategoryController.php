<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    /**
     * CategoryController constructor.
     * Cargamos el middleware auth excepto para metodos...
     */
    public function __construct() {
        $this->middleware('\App\Http\Middleware\ApiAuthMiddleware', ['except' => ['index', 'show']]);
    }

    /**
     * |        | GET|HEAD  | api/category                 | category.index   | App\Http\Controllers\CategoryController@index   | web                                       |
 * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $categories = Category::all();
        $data = [
            'status'     => 'success',
            'code'       => 200,
            'categories' => $categories,
        ];
        return response()->json($data, $data['code']);
    }


    /**
     * |        | POST      | api/category                 | category.store   | App\Http\Controllers\CategoryController@store   | web                                       |
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {

        // recoger datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        // validar datos
        $validate = \Validator::make($params_array, [
            'name' => 'required'
        ]);
        // guardar categoria
        if ($validate->fails() || isEmpty($params_array)) {
            $data = [
                'status'  => 'error',
                'code'    => 400,
                'message' => 'La categoria no se ha guardado',
            ];
            if (empty($params_array)) $data['message'] = 'No has enviado ningun nombre de categoria';
        } else {
            $category = new Category();
            $category->name = $params_array['name'];
            $category->save();

            $data = [
                'status'   => 'success',
                'code'     => 200,
                'category' => $category,
            ];
        }

        // devolver resultado
        return response()->json($data, $data['code']);

    }

    //|        | GET|HEAD  | api/category/create          | category.create  | App\Http\Controllers\CategoryController@create  | web                                       |


    //|        | DELETE    | api/category/{category}      | category.destroy | App\Http\Controllers\CategoryController@destroy | web                                       |
    //|        | PUT|PATCH | api/category/{category}      | category.update  | App\Http\Controllers\CategoryController@update  | web                                       |
    public function update($id, Request $request) {
        // recoger datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        // validar datos
        $validate = \Validator::make($params_array, [
            'name' => 'required'
        ]);

        // quitar lo que no queremos actualizar
        unset($params_array['id']);
        unset($params_array['created_at']);

        // actualizar el registro
        if ($validate->fails() || empty($params_array)) {
            $data = [
                'status'  => 'error',
                'code'    => 400,
                'message' => 'La categoria no se ha actualizado',
            ];
            if (empty($params_array)) $data['message'] = 'No has enviado ningun nombre de categoria para actualizar';
        } else {

            $category = Category::where('id', $id)->update($params_array);

            $data = [
                'status'   => 'success',
                'code'     => 200,
                'category' => $params_array,
            ];
        }

        // devolver resultado
        return response()->json($data, $data['code']);
    }
    /**
     * |        | GET|HEAD  | api/category/{category}      | category.show    | App\Http\Controllers\CategoryController@show    | web                                       |
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {
        $category = Category::find($id);
        if (is_object($category)) {
            $data = [
                'status'   => 'success',
                'code'     => 200,
                'category' => $category,
            ];
        } else {
            $data = [
                'status'  => 'error',
                'code'    => 400,
                'message' => 'La categoria no existe',
            ];
        }
        return response()->json($data, $data['code']);

    }

    //|        | GET|HEAD  | api/category/{category}/edit | category.edit    | App\Http\Controllers\CategoryController@edit    | web                                       |
    //|        | POST      | api/login                    |                  | App\Http\Controllers\UsersController@login      | web                                       |
}
