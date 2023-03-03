<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'id',
        'title',
        'description',
        'type',
        'sku',
        'id_categoria',
        'images'
    ];

    public static function rules()
    {
        return [
            'id' => 'required|unique:productos'
        ];
    }

    protected $table = 'productos';
    public $timestamps = false;
}
