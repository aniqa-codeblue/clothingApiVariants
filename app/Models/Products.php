<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $table = 'all_products';

    protected $fillable = ['name', 'description', 'price', 'quantity', 'size', 'color', 'images'];

    protected $casts = [
        'size' => 'array',
        'color' => 'array',
        'images' => 'array',
    ];
}
