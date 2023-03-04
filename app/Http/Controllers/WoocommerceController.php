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


    /**
     * Actualiza las categorias en la tabla de sincronizacion
     * @return array
     */
    public function updateCategory() {

         $this->pcService->getAllCategoriesAndSave();
         return \App\Models\Category::all();
    }

    /**
     * Actualiza las subcategorias en la tabla de sincronizacion
     * @return \Illuminate\Database\Eloquent\Collection
     */

    public function updateSubcategories(){
        $this->pcService->getAllSubCategoriesAndSave();
        return \App\Models\Category::all();
    }

    /**
     * Elimina las categorias de la tabla de sincronizacion
     * @return string
     */
    public function deleteCategory(){
        $this->pcService->deleteAllCategories();
        return "Categorias Borradas";
    }

    /**
     * Carga las categorias en woocomerce
     * @return string
     */
    public function loadCategoryWoo(){
        $this->woocomerceApi->loadCategoryWoo();
        return "Categorias Cargadas en Woocomerce";
    }

    /**
     * Elimina las categorias de woocomerce
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

    /**
     * Elimina los productos de Woocomerce
     * @return string
     */
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

    public function updateProducts(){
        return $this->pcService->updateStockAndPrice();
    }

    /**
     * Carga los productos en woocomerce a partir de la tabla de sincronizacion
     * @return mixed
     */
    public function loadProductWoo(){
        return $this->woocomerceApi->loadProductsToWooCommerce();
    }



}
