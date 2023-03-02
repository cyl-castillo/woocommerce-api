<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TokenStarCenterMiddleware
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
        $response = Http::post(env("STARCENTER_URL")."api/login/", [
            'username' => env('STARCENTER_USER'),
            'password' => env('STARCENTER_PASSWORD')
        ]);

        $data = json_decode($response->getBody(), true);
        $token = $data['token'];
        \session(['center_tok' => $token]);


        return $next($request);
    }
}
