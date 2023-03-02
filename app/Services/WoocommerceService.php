<?php

namespace App\Services;

use App\Helpers\FunctionsHelper;
use Codexshaper\WooCommerce\Facades\Product;
use Codexshaper\WooCommerce\Models\Category;

class WoocommerceService
{

    private PCServiceAPI $pCServiceAPI;

    public function __construct(PCServiceAPI $pCServiceAPI)
    {
        $this->pCServiceAPI = $pCServiceAPI;
    }

    public function loadProductsToWooCommerce(){
        $productsFromPCServices = $this->pCServiceAPI->getAllProduct();
        $categoryIdsAss = [];
        foreach ($productsFromPCServices as $product) {
            $arrayCategories = $product['categories'];
            $categoryIds = [];
            $categoryTitles = [];
            foreach ($arrayCategories as $arrayCategory) {
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
            $data = [
                'name' => $product['description'],
                'type' => 'simple',
                'short_description' => $product['description'],
                'description' => $product['body'],
                'sku' => $product['sku'],
                'price' => $product['price']['price'],
                'regular_price' => strval($product['price']['price']),
                'stock_quantity' => 10,
                'categories' => $categoryIds,
                'images' =>  [
                    [
                        'src' => $product['images'][0]['variations'][1]['url']
                    ]
                ],

            ];
            Product::create($data);
        }
        return Product::all();
    }

}
