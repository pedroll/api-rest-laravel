<?php

namespace App\Http\Middleware;

use Closure;
use JwtAuth;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        // comprobar usuario autentificado
        $token = $request->header('Authorization');
        $token = str_replace('"', '', $token);

        $jwAuth = new JwtAuth();
        $checktoken = $jwAuth->checkToken($token);

        if ($checktoken) {
            return $next($request);

        } else {
            $data = array(
                'status'  => 'error',
                'code'    => '400',
                'message' => 'El usuario no esta identicado2',
            );

            return response()->json($data, $data['code']);
        }
    }
}
