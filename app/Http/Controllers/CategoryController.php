<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    //|        | GET|HEAD  | api/category                 | category.index   | App\Http\Controllers\CategoryController@index   | web                                       |
    /**
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

    //|        | POST      | api/category                 | category.store   | App\Http\Controllers\CategoryController@store   | web                                       |
    public function store($category) {

    }

    //|        | GET|HEAD  | api/category/create          | category.create  | App\Http\Controllers\CategoryController@create  | web                                       |


    //|        | DELETE    | api/category/{category}      | category.destroy | App\Http\Controllers\CategoryController@destroy | web                                       |
    //|        | PUT|PATCH | api/category/{category}      | category.update  | App\Http\Controllers\CategoryController@update  | web                                       |

    //|        | GET|HEAD  | api/category/{category}      | category.show    | App\Http\Controllers\CategoryController@show    | web                                       |
    /**
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
