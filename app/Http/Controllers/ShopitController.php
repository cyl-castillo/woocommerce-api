<?php

namespace App\Http\Controllers;

use App\Models\PriceShopit;

class ShopitController extends Controller
{
    public function confgiPrice($config){
        return PriceShopit::create($config);
    }
}
