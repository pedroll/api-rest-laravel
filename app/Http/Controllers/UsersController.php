<?php

namespace App\Http\Controllers;

use http\Message;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function pruebas(Request $request)
    {

        Return " Accion de pruebas de USER-CONTROLER";
    }

    public function register(Request $request)
    {
        // recoger datos del post


        // validar datos


        // cifrar contraseña


        //c omprobar usuario existe


        // crear usuario


        //b recogemos variables del post
        $name = $request->input('name');
        $surname = $request->input('surname');

        $data = array(
            'status' => 'error',
            'code' => '400',
            'message' => 'El usuario no se ha creado'
        );

        //        Return " Accion de registro de usuario: $name $surname";
        return response()->json($data, $data['code']);
    }

    public function login(Request $request)
    {

        Return " Accion de login de USER-CONTROLER";
    }
}
