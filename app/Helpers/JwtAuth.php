<?php
/**
 * Created by IntelliJ IDEA.
 * User: pedro
 * Date: 2019-06-01
 * Time: 16:30
 */

namespace App\Helpers;

//importamos libreria
Use Firebase\JWT\JWT;

// importamos ORM laravel
//Use Illuminate\Support\Facades\DB;

// importamos modelo
Use App\User;

class JwtAuth
{
    /**
     * @var string
     */
    public $key;

    /**
     * JwtAuth constructor.
     */
    public function __construct() {
        $this->key = 'esto_es_una_clave_super_secreta-8484846498484';
    }


    /**
     *
     * @param $email
     * @param $password
     * @param null $getToken
     * @return string
     */
    public function signup($email, $password, $getToken = null) {
        // buscar si existe usuario con ese mail
        $user = User::where([
            'email'    => $email,
            'password' => $password
        ])->first();
        // comprobar si son correcto
        $signup = false;
        if (is_object($user)) {
            $signup = true;
        }

        // generar el token con los datos del usuario identificado
        if ($signup) {
            $token = [
                'sub'     => $user->id,
                'email'   => $user->email,
                'name'    => $user->name,
                'surname' => $user->surname,
                'iat'     => time(), // cuando es creado el token
                'exp'     => time() + (7 * 24 * 60 * 60) //expirara en una semana
            ];

            $jwt = JWT::encode($token, $this->key, 'HS256');
            $jwtDecoded = JWT::decode($jwt, $this->key, ['HS256']);
            //Devolber los datos decodificado o el token en funcion de un parametro
            if (is_null($getToken)) {
                $data = $jwt;
            } else {
                $data = $jwtDecoded;
            }
        } else {
            $data = [
                'status'  => 'error',
                'message' => 'Login incorrecto'
            ];
        }
        return $data;
    }

    /**
     * @param $jwt
     * @param bool $getIdentity
     * @return bool|object|null
     */
    public function checkToken($jwt, $getIdentity = false) {
        //$auth = false;
        $decoded = null;
        try {
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            $decoded = str_replace($decoded, '"', '');
        } catch (\UnexpectedValueException $e) {
            // $auth = false;
        } catch (\DomainException $e) {
            //$auth = false;
        }

        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }

        if ($getIdentity) return $decoded;

        return $auth;
    }

}
