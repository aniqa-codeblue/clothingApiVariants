<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';        //table name

    protected $fillable = ['name', 'description', 'price', 'quantity'];

    // protected $casts = [
    //     'size' => 'array',
    //     'color' => 'array',
    //     'images' => 'array',
    // ];

    public function images() {
        return $this->hasMany(ProductImages::class);
    }

    public function variants() {
        return $this->hasMany(ProductVariants::class);
    }
}
