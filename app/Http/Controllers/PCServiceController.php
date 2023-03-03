<?php

namespace App\Http\Controllers;


use App\Models\TokenApi;
use Illuminate\Support\Facades\Http;

class PCServiceController extends Controller
{


    public function login(){

        $response = Http::post(env("PCSERVICE_URL")."auth/login/", [
            'username' => env('PCSERVICE_USER'),
            'password' => env('PCSERVICE_PASSWORD')
        ]);

        $data = json_decode($response->getBody(), true);
        $token = $data['token'];

        TokenApi::create($token);
        return $token;
    }

}
