<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class Permiso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(in_array($request->path(),session('dataUsuario')['rutas_disponibles'])){
            return $next($request);
        }else{
            if($request->ajax()){
                $html = "<div class='alert alert-danger text-center'>
                            <h5>No tienes permisos para visualizar esta área</h5>
                        </div>";
                return response($html);
            }else{
                return new Response(view('layouts.partials.acceso_denegado'));
            }

        }
    }
}
