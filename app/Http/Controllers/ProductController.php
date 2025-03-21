<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function stores_product(Request $request){
        //dd()
      $validator = Validator::make($request -> all(), [
           'name' => 'required|string|max:255',
           'description' => 'nullable|string',
           'price' => 'required|numeric|min:0',
           'quantity' => 'required|numeric|min:1',
           'size' => 'required',
           'color' => 'required',
           'images' => 'nullable|array',
           'images.*' => 'file|image|mimes:jpg,jpeg,png|max:2048',
       ]);

       //$sizes = is_array($request->input('size')) ? $request->input('size') : explode(',', $request->input('size'));
       //$colors = is_array($request->input('color')) ? $request->input('color') : explode(',', $request->input('color'));

       // $imagePath = null;
       // if ($request->hasFile('image')) {
       //     $image = $request->file('image');
       //     $filename = $image->getClientOriginalName(); // Keep the original filename
       //     $image->move(public_path('Images'), $filename);
       //     $imagePath = 'public\Images' . $filename;
       // }

       if ($validator->fails()) {
           return response()->json(['errors' => $validator->errors()], 422);
       }

       // D:\productshoptask\storage\app\public\Images
       // Handle image uploads
       $imageData = [];
       if ($request->hasFile('images')) {
           foreach ($request->file('images') as $image) {
               $imageName = $image->getClientOriginalName();
               $image->move(public_path('resources/views/images'), $imageName);
               $imagePath = 'resources/views/images/' . $imageName;
               $imageData[] = $imagePath;
       }}

       $product = Products::create([
           'name' => $request->input('name'),
           'description' => $request->input('description'),
           'price' => $request->input('price'),
           'quantity' => $request->input('quantity'),
           'size' => $request->input('size'),
           'color' => $request->input('color'),
           'images' => $imageData, // Store image data as JSON
       ]);

       return response()->json([
           'message' => 'Product created successfully!',
           'product' => $product
       ], 201);
   }

   public function index() {
       $products = Products::all();
       
       return response()->json($products);
   }

   public function show($id) {

       $product = Products::findOrFail($id);

       // $products->each(function ($product) {
       //     $product->size = json_decode($product->size, TRUE);
       //     $product->color = json_decode($product->color, TRUE);
       //     $product->images = json_decode($product->images, TRUE);
       // });
       return response()->json($product);
   }


   public function display_card()
   {
       $products = Products::select('id','name', 'description', 'price', 'quantity', 'size', 'color', 'images')->get();
       return response()->json($products);
   }

   public function display_details()
   {
       $products = Products::select('id', 'name', 'description', 'images', 'price')->get();
       return response()->json($products);
   }
}
