<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Models\Products;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([HandleCors::class])->group(function () {
});

Route::post('/save_products', [ProductController::class, 'stores_product']);

Route::get('/display_products', [ProductController::class, 'index']);

// Route::get('/product', function () {
//     return view('product_card');
// });

Route::get('/show_product/{id}', function ($id) {
    $product = Products::find($id); // Fetch product by ID
    Log::info("Product Retrieved", ['product' => $product]); // Log product info

    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    return response()->json($product->toArray()); // Convert model to array
});

Route::post('/products', [ProductController::class, 'store']);
Route::get('/product/{id}', [ProductController::class, 'show']);

Route::get('/product_detail', [ProductController::class, 'index']);

Route::get('/api/product_card', [ProductController::class, 'display_card']);


Route::get('/all_products', function() {
    return view('product_gallery');
});