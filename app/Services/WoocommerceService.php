<?php

namespace App\Services;

use App\Helpers\FunctionsHelper;
use App\Repositories\CategoryRepository;
use Codexshaper\WooCommerce\Facades\Product;
use Codexshaper\WooCommerce\Models\Category;
use function Symfony\Component\String\u;

class WoocommerceService
{

    private PCServiceAPI $pCServiceAPI;
    private CategoryRepository $categoryRepository;

    public function __construct(PCServiceAPI $pCServiceAPI, CategoryRepository $categoryRepository)
    {
        $this->pCServiceAPI = $pCServiceAPI;
        $this->categoryRepository = $categoryRepository;
    }

    public function loadCategoryWoo(){

        $categories = \App\Models\Category::all()->all();
        $categoryTitles = [];
        foreach ($categories as $arrayCategory) {
            $data = [
                'name' => $arrayCategory['title'],
                'image' => $arrayCategory['image']
            ];

            try {
                //Verificando para no isntertar dobles las categorias
                if (!FunctionsHelper::verifyIfExistTitle($categoryTitles, $arrayCategory['title'])) {
                    $category = Category::create($data);
                    $categoryTitles[] = $arrayCategory['title'];
                    $updateCat = \App\Models\Category::find($arrayCategory['id']);
                    $updateCat->id_woo = $category->id;
                    $updateCat->save();

                }

            } catch (\Exception $e) {

            }
        }

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
