<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{


    protected $fillable = [
        'id',
        'title',
        'description',
        'type',
        'image',
        'od_categoria'
    ];

    public static function rules()
    {
        return [
            'id' => 'required|unique:categorias'
        ];
    }

    protected $table = 'categorias';
    public $timestamps = false;


}
