<?php

namespace App\Http\Middleware;

use App\Models\TokenApi;
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
        $response = $next($request);

        // Si el token ha expirado, renovar el token y guardar el nuevo token en la sesión
            if ($response->status() === 401) {
                $newToken = $this->refreshToken();

                if ($newToken) {
                    $request->session()->put('pcs_tok', $newToken);
                    TokenApi::created(['token' => $newToken]);
                    $response = $next($request);
                }
            }


        return $response;
    }

    private function isAuthenticated(Request $request)
    {
        return $request->session()->has('pcs_tok');
    }

    private function refreshToken()
    {
                $response = Http::post(env("PCSERVICE_URL")."auth/login/", [
                'username' => env('PCSERVICE_USER'),
                'password' => env('PCSERVICE_PASSWORD')
            ]);

        $data = json_decode($response->getBody(), true);
        return $data['token'];
    }






}
