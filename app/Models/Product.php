<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'short_description',
        'product_price',
        'minimum_quantity',
        'product_description',
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}


