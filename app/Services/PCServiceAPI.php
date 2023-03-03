<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class PCServiceAPI
{

    private $url;
    private CategoryRepository $categpryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categpryRepository = $categoryRepository;
        $this->url = env("PCSERVICE_URL");
    }

    public function getAllCategoriesAndSave(){

        $data = Http::withToken(Session::pull('pcs_tok'))->get($this->url.'categories/')->json();
        foreach ($data as $dat) {
            $image = null;
            if (count($dat['images']) > 0) {
                $image = $dat['images'][0]['variations'][0]['url'];
            }
            $category = [
                'id' => $dat['id'],
                'title' => $dat['title'],
                'description' => $dat['description'],
                'type' => $dat['type'],
                'image' => $image,
            ];
            $this->categpryRepository->save($category);
        }
    }



    public function  getAllSubCategoriesAndSave(){


        $categories = $this->categpryRepository->getAll()->all();

        foreach ($categories as $cat) {

            $data = Http::withToken(Session::get('pcs_tok'))->get($this->url . 'categories/' . $cat['id'])->json();

            if (isset($data['childs'])) {
                foreach ($data['childs'] as $dat) {

                    $image = null;
                    if (count($dat['images']) > 0) {
                        $image = $dat['images'][0]['variations'][0]['url'];;
                    }
                    $category = [
                        'id' => $dat['id'],
                        'title' => $dat['title'],
                        'description' => $dat['description'],
                        'type' => $dat['type'],
                        'image' => $image,
                        'od_categoria' => $cat['id']
                    ];

                    $this->categpryRepository->save($category);

                }
            }
        }

    }

    public function deleteAllCategories(){
        $categories = Category::all();
        foreach ($categories as $cat){
            $cat->delete();
        }
    }

    public function getAllProduct(){

        $categorias = Category::where('od_categoria', '<>', null)->get()->all();



        foreach($categorias as $cat){

            $data = Http::withToken(env('PCSERVICE_TOKEN'))->get($this->url."categories/".$cat['od_categoria']."/".$cat['id']."/products")->json();;

            if(isset($data['childs'])){

                if(isset($data['childs'][0]['products'])){

                    $productos = $data['childs'][0]['products'];

                    foreach($productos as $product){

                        $images = [];
                        if(count($product['images']) > 0){
                            foreach($product['images'] as $img){
                                $imgReuturn = '';
                                foreach($img['variations'] as $var){
                                    $imgReuturn = $var['url'];
                                }
                                $images [] = $imgReuturn;
                            }
                        }

                        $datas = [
                            'id' => $product['id'],
                            'title' => $product['title'],
                            'description' => $product['description'],
                            'type' => $product['type'],
                            'sku' => $product['sku'],
                            'id_categoria' => $cat['id'],
                            'images' => json_encode($images),
                        ];

                        $prods = Product::where('id', $product['id'])->get()->all();
                        if(count($prods) == 0){
                            Product::create($datas);
                        }

                    }


                }
            }
        }


    }

}
