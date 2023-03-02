<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    protected $category;

    /**
     * @param $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function getAll(){
        return $this->category->all();
    }

    public function save($cat){
        return $this->category->create($cat);
    }

    public function delete($id){

        $category = $this->category->find($id);
        if ($category instanceof Category)
            return $category->delete();
    }


}
