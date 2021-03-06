<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\User;
//use http\Message;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;


/**
 * Class UsersController
 * @package App\Http\Controllers
 */
class UsersController extends Controller
{
    /**
     * @param Request $request
     * @return string
     */
    public function pruebas(Request $request) {

        Return " Accion de pruebas de USER-CONTROLER";
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        // recoger datos del post dede un json o null
        /* $name = $request->input('name');
         $surname = $request->input('surname');*/
        $json = $request->input('json', null);
        var_dump($json);

        //sacamos un json
        // $params = json_decode($json);
        // con el true sacamos array
        $params_array = json_decode($json, true);

        // Limpiar datos
        $params_array = array_map('trim', $params_array);

        // validar datos
        // podemos llamar al shorcut o utilizar el USE con el namespace
        if (empty($params_array)) {
            $data = array(
                'status'  => 'error',
                'code'    => '404',
                'message' => 'los udatos no son correctos'
            );
            return response()->json($data, 404);
        }

        $validate = \Validator::make($params_array, [
            'name'     => 'required|alpha',
            'surname'  => 'required|alpha',
            'email'    => 'required|email|unique:users',            //  comprobar usuario existe
            'password' => 'required',
        ]);


        if ($validate->fails()) {
            // valicacion ha fallado
            $data = array(
                'status'  => 'error',
                'code'    => '404',
                'message' => 'El usuario no se ha creado',
                'errors'  => $validate->errors()
            );
            return response()->json($data, 404);
        } else {
            // valicacion pasada correctamente

            // cifrar contraseña
            // $pwd = password_hash($params_array['password'], PASSWORD_BCRYPT, ['cost' => 4]);
            $pwd = hash('sha256', $params_array['password']);

            //  comprobar usuario existe
            // vomprobado en validacion

            //  crear usuario
            $user = new User();
            $user->name = $params_array['name'];
            $user->surname = $params_array['surname'];
            $user->email = $params_array['email'];
            $user->password = $pwd;
            $user->role = 'ROLE_UESER';

            // guardamos usuario
            $user->save();

            $data = array(
                'status'  => 'succes',
                'code'    => '200',
                'message' => 'El usuario se ha creado'
            );
            return response()->json($data, 200);
        }


        //        Return " Accion de registro de usuario: $name $surname";
        // return response()->json($data, $data['code']);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request) {
        $jwt = new JwtAuth();

        // recibir datos post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        //validar datos
        $validate = \Validator::make($params_array, [
            'name'  => 'required|alpha',
            'email' => 'required|email',            //  comprobar usuario existe
        ]);
        if ($validate->fails()) {
            // valicacion ha fallado
            $signup = array(
                'status'  => 'error',
                'code'    => '404',
                'message' => 'El usuario no se ha podido identificar',
                'errors'  => $validate->errors()
            );
        } else {
            //cifrar la password
            $pwd = hash('sha256', $params->password);
            $getToken = null;
            if (!empty($params_array->getToken)) {
                $getToken = true;
            }
            //devolver token o datos
            $signup = $jwt->signup($params->email, $pwd, $getToken);
        }


        // 2	Pedro	LLongo	pedrollongo@gmail.com	$2y$04$K8yY04sRcgKbvtu.qo3kieDiychPY1pFvxt5kSmY4LvlqyarY.JXy				2019-06-01 09:56:24	2019-06-01 09:56:24
        //$email = 'pedrollongo@gmail.com';
        //$password= 'pedro';
        // $pwd = password_hash( $password, PASSWORD_BCRYPT, ['cost' => 4]);
        //           $pwd = hash('sha256', $password);
        // $pwd = Hash::make($password);
        //var_dump($pwd);die;
        return response()->json($signup, 200);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request) {

        // comprobar usuario autentificado
        // nos lo hemos llevado al middleware
        $token = $request->header('Authorization');
        $token = str_replace('"', '', $token);
        //
        $jwAuth = new JwtAuth();
        // $checktoken = $jwAuth->checkToken($token);

        // actualizar usuario
        // recoger datos post
        $json = $request->input('json', null);
        // $params = json_decode($json);
        $params_array = json_decode($json, true);

        // no hace falta comprobar $checktoken esta comprobado en middleware
        if (!empty($params_array)) {

            // sacar id usuario identificado
            $user = $jwAuth->checkToken($token, true);


            // Limpiar datos
            $params_array = array_map('trim', $params_array);

            // validar datos
            // podemos llamar al shorcut o utilizar el USE con el namespace
            if (empty($params_array)) {
                $data = array(
                    'status'  => 'error',
                    'code'    => '404',
                    'message' => 'los udatos no son correctos'
                );
                return response()->json($data, 404);
            }

            $validate = \Validator::make($params_array, [
                'name'    => 'required|alpha',
                'surname' => 'required|alpha',
                'email'   => 'required|email|unique:users,' . $user->sub,            //  comprobar usuario existe excepto el id del usuario
            ]);

            // quitamos los que no queremos actualizar
            unset($params_array['id']);
            unset($params_array['password']);
            unset($params_array['role']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);

//            if ($validate->fails()) {
//                // valicacion ha fallado
//                $data = array(
//                    'status'  => 'error',
//                    'code'    => '404',
//                    'message' => 'El usuario no se ha actualizado',
//                    'errors'  => $validate->errors()
//                );
//                return response()->json($data, $data['code']);
//            } else {
//
//                          }

// actualizar usuario en base de datos
            $user_update = User::where('id', $user->sub)->update($params_array);

            // devolver array
            $data = array(
                'status'  => 'sucess',
                'code'    => '200',
                'user'    => $user,
                'changes' => $params_array);
            return response()->json($data, $data['code']);

        } else {
            $data = array(
                'status'  => 'error',
                'code'    => '400',
                'message' => 'Error al subir imagen',
            );

            return response()->json($data, $data['code']);

        }

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function upload(Request $request) {

        // recoger datos peticionm
        $image = $request->file('file0');

        // validacion imagen

        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ], ['Tiene que ser una imagen valida']);
        // guardar imagen
        if ($image && !$validate->fails()) {

            $image_name = time() . $image->getClientOriginalName();
            // discos virtuales
            \Storage::disk('users')->put($image_name, \File::get($image));

            $data = array(
                'status'  => 'success',
                'code'    => '200',
                'image'   => $image_name,
                'message' => 'Imagen subida',
            );

        } else {

            $data = array(
                'status'   => 'error',
                'code'     => '400',
                'message'  => 'Error al subir la imagen',
                'message2' => $validate->errors()->all()
            );


        }

        return response()->json($data, $data['code']);

    }

    /**
     * @param $filename
     * @return \Illuminate\Http\JsonResponse|\Response
     */
    public function getImage($filename) {
        $isset = \Storage::disk('users')->exists($filename);
        try {
            $file = \Storage::disk('users')->get($filename);
        } catch (FileNotFoundException $e) {
            $data = array(
                'status'  => 'error',
                'code'    => 400,
                'message' => $e->getMessage(),
            );
            if (!$isset) $data['message'] = 'imagen no existe';

            return response()->json($data, $data['code']);
        }
        // da erro al devolver objeto
        return new \Response($file, 200);
    }

    public function detail($id) {
        $user = User::find($id);
        if (is_object($user)) {
            $data = array(
                'status' => 'success',
                'code'   => 200,
                'user'   => $user,
            );

        } else {
            $data = array(
                'status'  => 'error',
                'code'    => 400,
                'message' => 'El usuario no existe',
            );
        }

        return response()->json($data, $data['code']);

    }
}
