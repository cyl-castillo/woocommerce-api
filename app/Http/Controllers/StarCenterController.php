<?php

namespace App\Http\Controllers;

use App\Services\StarCenterService;
use Codexshaper\WooCommerce\Models\Category;
use Illuminate\Http\Request;

class StarCenterController extends Controller
{

    private StarCenterService $startcenterService;

    /**
     * @param StarCenterService $startcenterService
     */
    public function __construct(StarCenterService $startcenterService)
    {
        $this->startcenterService = $startcenterService;
    }


    public function loadProductsCenter(){

    }

    public function loadCategoriesCenter(){
     return $this->startcenterService->loadCategoriesCenter();
    }
    public function synCategoriesCenter(){
        $this->startcenterService->syncCategoriesCenter();
        return Category::all();
    }

}
