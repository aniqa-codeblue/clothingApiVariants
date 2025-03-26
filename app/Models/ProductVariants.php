<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariants extends Model
{
    use HasFactory;

    protected $table = 'product_variants';  //table name

    protected $fillable = ['product_id', 'colors_id', 'sizes_id', 'stock_quantity'];

    public function size() {
        return $this->belongsTo(ProductSizes::class);
    }

    public function color() {
        return $this->belongsTo(ProductColors::class, 'colors_id');
    }

    public function product() {
        return $this->belongsTo(Products::class);
    }

}
