<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetTimeoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = Http::withOptions([
            'timeout' => 300 // tiempo de espera en segundos
        ])->middleware($next($request));

        return $response;
    }
}
