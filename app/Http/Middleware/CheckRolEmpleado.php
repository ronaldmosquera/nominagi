<?php

namespace App\Http\Middleware;

use Closure;

class CheckRolEmpleado
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
        if (in_array('EMPLOYEE',session('dataUsuario')['user_type'])) {
            return $next($request);
        } else {
            return response(redirect('https://www.google.com'));
        }
    }
}
