<?php

namespace App\Http\Controllers;

use App\Helpers\FunctionsHelper;
use App\Services\PCServiceAPI;
use App\Services\WoocommerceService;
use Codexshaper\WooCommerce\Facades\Product;
use Codexshaper\WooCommerce\Models\Category;

class WoocommerceController extends Controller
{

    private $pcService;

    private $woocomerceApi;


    /**
     * @param PCServiceAPI $pcService
     */
    public function __construct(PCServiceAPI $pcService, WoocommerceService $woocomerceApi)
    {
        $this->pcService = $pcService;
        $this->woocomerceApi = $woocomerceApi;
    }

    public function test(){
        $test = "Hola";
        return $test;
    }

    public function loadProductsss(){

        $productsFromPCServices = $this->pcService->getAllProduct();

        $cont = 0;
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



                $pro = '0';
                if ($cont < 10){
                    $data = [
                        'name' => $product['title'],
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
                    $cont++;
                } else {
                    return;
                }

        }
        return Product::all();
    }


    /**
     * @return array
     */
    public function updateCategory() {

         $this->pcService->getAllCategoriesAndSave();
         return \App\Models\Category::all();
    }


    public function updateSubcategories(){
        $this->pcService->getAllSubCategoriesAndSave();
        return \App\Models\Category::all();
    }

    public function deleteCategory(){
        $this->pcService->deleteAllCategories();
        return "Categorias Borradas";
    }

    public function loadCategoryWoo(){
        $this->woocomerceApi->loadCategoryWoo();
        return "Categorias Cargadas en Woocomerce";
    }

    /**
     * @return string
     */
    public function deleteCategoryWoo(){
        $options = ['per_page' => 100];
        $categories = Category::all($options);
        $data = [];
        foreach ($categories as $category) {
            $data[] = $category->id;
        }
        $dataToDelete = [
            'delete' => $data
        ];
        Category::batch($dataToDelete);

        return "Categorias Borradas";
    }

    public function deleteProducts(){
        $options = ['per_page' => 100];
        $products = Product::all($options);
        $data = [];
        foreach ($products as $product) {
            $data[] = $product->id;
        }
        $dataToDelete = [
            'delete' => $data
        ];
        Product::batch($dataToDelete);
        return "Productos Borrados";
    }

    public function loadProducts(){
        $this->pcService->getAllProduct();
        return "Productos cargados";
    }

}
