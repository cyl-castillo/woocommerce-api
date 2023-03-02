<?php

namespace App\Services;

use App\Helpers\FunctionsHelper;
use App\Models\CategoriasStarCenter;
use Codexshaper\WooCommerce\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class StarCenterService
{

    private $url;

    public function __construct()
    {
        $this->url = env("STARCENTER_URL");
    }
    public function loadProductsCenter(){

    }

    public function loadCategoriesCenter(){

        $response = Http::get($this->url.'categories',[
            'token' => Session::pull('pcs_tok')
        ]);


        foreach ($response->json() as $item) {
            $data = [
                'title' => $item['title'],
                'id_center' => $item['id'],
            ];
                CategoriasStarCenter::create($data);
        }

        return $response->json();
    }

    public function syncCategoriesCenter(){
        $arrayCategories = CategoriasStarCenter::all();
        $categoryIds = [];
        $categoryTitles = [];
        foreach ($arrayCategories as $arrayCategory) {
            var_dump($arrayCategory);
            $data = [
                'name' => $arrayCategory['title']
            ];
            $categoryTitles[] = $arrayCategory['title'];
            try {
                //Verificando para no isntertar dobles las categorias
                if (!FunctionsHelper::verifyIfExistTitle($categoryTitles, $arrayCategory['title'])){
                    $category = Category::create($data);
                    $categoryIds[] = ['id' => $category['id']];
                    $categoryIdsAss = [$arrayCategory['title'] => $category['id']];
                } else {
                    $categoryIds[] = ['id' => $categoryIdsAss[$arrayCategory['title']]];
                }


            } catch (\Exception $e){

            }

        }

    }
}
