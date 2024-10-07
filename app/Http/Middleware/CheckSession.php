<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class CheckSession
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
        if(session('dataUsuario')!=null && !!session('dataUsuario')['logged']){
            return $next($request);
        }else{
            return redirect()->route('login');
            //return view('layouts.prueba');
        }
    }
}
