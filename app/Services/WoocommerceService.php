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
        $arrayDatas = [];
        foreach ($categories as $arrayCategory) {
            $data = [
                'name' => $arrayCategory['title'],
            ];
            if ($arrayCategory['od_categoria'] != null)
                $data['parent'] = \App\Models\Category::find($arrayCategory['od_categoria'])['id_woo'];

            if ($arrayCategory['image'] != null)
                $data['image'] = ['src' => $arrayCategory['image']];

            try {
                //Verificando para no isntertar dobles las categorias
                if (!FunctionsHelper::verifyIfExistTitle($categoryTitles, $arrayCategory['title'])) {
                    $category = Category::create($data);
                    $categoryTitles[] = ['title' => $arrayCategory['title'], 'id_woo' => $category->get('id')];
                    $updateCat = \App\Models\Category::find($arrayCategory['id']);
                    $updateCat->id_woo = $category->get('id');
                    $updateCat->save();

                } else {
                    $updateCat = \App\Models\Category::find($arrayCategory['id']);
                    $updateCat->id_woo = FunctionsHelper::getIdWooByTitle($categoryTitles,$arrayCategory['title']);
                    $updateCat->save();
                }

            } catch (\Exception $e) {

                $e->getMessage();

            }
        }

    }

    public function loadProductsToWooCommerce(){
        $productsFromPCServices = \App\Models\Product::where('id_woo', null)->get()->all();

        $dataToInserts = [];
        $cont = 0;
        foreach ($productsFromPCServices as $product) {

            $categoryIds = [];
            $firstCat = \App\Models\Category::find($product['id_categoria'])->id_woo;
            if ($firstCat != null)
                $categoryIds[] = ['id' => $firstCat];

            $subcategories = \App\Models\Category::where('od_categoria', $product['id_categoria'])->get();
            foreach ($subcategories as $subcategory) {
                if ($subcategory->id_woo != null)
                    $id = $subcategory->id_woo;

                $categoryIds[] = [
                    'id' => $id
                ];
            }

            $imagArr = [];
            foreach (json_decode($product['images']) as $image) {
                $imagArr[] = ['src' => $image];
            }

            $data = [
                'name' => $product['title'],
                'type' => 'simple',
                'short_description' => $product['description'],
                'description' => $product['body'],
                'sku' => $product['sku'],
                'price' => strval(floatval($product['price_original']) * 41),
                'regular_price' => strval(floatval($product['price'])),
                'stock_quantity' => $product['stock'],
                'categories' => $categoryIds,
                'images' => $imagArr,

            ];

            $prod = Product::create($data);
            $temp = \App\Models\Product::find($product['id']);
            $temp->id_woo = $prod['id'];
            $temp->save();

        }
        return Product::all();

    }

}
