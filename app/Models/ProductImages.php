<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImages extends Model
{
    use HasFactory;

    protected $table = 'product_images';         //table name

    protected $fillable = ['product_id', 'image_url'];

    public function product() {

        return $this->belongsTo(Products::class);
    }
}
