<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class TokenMiddleware
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

            $response = Http::post(env("PCSERVICE_URL")."auth/login/", [
                'username' => env('PCSERVICE_USER'),
                'password' => env('PCSERVICE_PASSWORD')
            ]);

            $data = json_decode($response->getBody(), true);
            $token = $data['token'];
            \session(['pcs_tok' => $token]);


        return $next($request);
    }
}
