<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceShopit extends Model
{
    use HasFactory;

    protected $fillable = [
        'iva',
        'plus',
        'conversion',
    ];
}
