<?php

namespace App\Services;

use App\Models\Category;
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
        return Http::withToken(Session::pull('pcs_tok'))->get($this->url.'products/bydate/?from='.date('Ymd'))->json();
    }

}
