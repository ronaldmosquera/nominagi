<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        /*'/',
         'http://nomina.innofarm.com.ec/hash',
        'http://prueba109.serprodatos.ec/hash',
        'http://nomina.serdimed.com.ec/hash',
        'http://nomina.innoclinica.ec/hash',
        'http://nominnoclinica.innofarm.com.ec/hash',
        'http://innofarmnomina.develop/hash',
        'http://nomina.innofarm.com.ec/logout',
        'http://prueba109.serprodatos.ec/logout',
        'http://nomina.serdimed.com.ec/logout',
        'http://nominnoclinica.innofarm.com.ec/logout',
        'http://innofarmnomina.develop/logout',
        'http://nominainnofarm.develop/logout',
        'http://nominainnofarm.develop/hash', */
    ];
}
