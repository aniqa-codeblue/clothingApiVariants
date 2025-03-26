<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\facades\Validator;
use App\Models\Products;
use App\Models\ProductVariants;
use App\Models\ProductImages;
use App\Models\ProductColors;
use App\Models\ProductSizes;

class ProductVariantController extends Controller
{
    public function index() {
        $products = Products::with(['variants.size', 'variants.color', 'images'])->get();
        return response()->json($products);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:1',
            'images' => 'array',
            'images.*' => 'file|image|mimes:jpg,jpeg,png|max:2048', //image urls
            'variants' => 'array',
            'variants.*.size' => 'required|string|max:50',
            'variants.*.color' => 'required|string|max:50',
            'variants.*.stock_quantity' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=> $validator->errors()], 422);
        };

        $product = Products::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity
        ]);

        if ($request->hasFile('images')) { 
            $imageData = []; // Array to store image paths
            
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName(); // Add timestamp to avoid duplicates
                $image->move(public_path('images'), $imageName);
                $imagePath = 'images/' . $imageName;
                
                ProductImages::create([
                    'product_id' => $product->id,
                    'image_url'  => $imagePath // Store the path as a string
                ]);

                $imageData[] = $imagePath;
            }
        }

        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {

            // Find or Create Size
            $size = ProductSizes::firstOrCreate(
                ['sizes' => $variant['size']]
            );

            // Find or Create Color
            $color = ProductColors::firstOrCreate(
                ['colors' => $variant['color']]
            );

                ProductVariants::create([
                    'product_id' => $product->id,
                    'colors_id' => $color->id,
                    'sizes_id' => $size->id,
                    'stock_quantity' => $variant['stock_quantity']
                ]);
            }
        }

        return response()->json(['message' => 'Product created successfully!', 'product' => $product]);
    }

    public function updateProduct(Request $request, $id) {

        $product = Products::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // $validator = Validator::make($request->all(), [
            
        //     'name' => 'sometimes|string|max:255',
        //     'description' => 'sometimes|nullable|string',
        //     'price' => 'sometimes|numeric|min:0',
        //     'quantity' => 'sometimes|numeric|min:1',
        //     'images' => 'sometimes|array',
        //     'images.*' => 'sometimes|file|image|mimes:jpg,jpeg,png|max:2048', //image urls
        //     'variants' => 'sometimes|array',
        //     'variants.*.size' => 'sometimes|string|max:50',
        //     'variants.*.color' => 'sometimes|string|max:50',
        //     'variants.*.stock_quantity' => 'sometimes|numeric|min:1',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['errors'=> $validator->errors()], 422);
        // };

        // $product = new Products;
        // $product->name = $request->name;
        // $product->description = $request->description;
        // $product->price = $request->price;
        // $product->quantity = $request->quantity;
        // $product->save();

        // Update Product
        // $product->update([
        //     'name'        => $request->name,
        //     'description' => $request->description,
        //     'price'       => $request->price,
        //     'quantity'    => $request->quantity
        // ]);

        $product->update($request->only(['name', 'description', 'price', 'quantity']));

        if ($request->has('variants')) {
            ProductVariants::where('product_id', $id)->delete();
            foreach ($request->variants as $variant) {

            // Find or Create Size
            $size = ProductSizes::firstOrCreate(
                ['sizes' => $variant['size']]
            );

            // Find or Create Color
            $color = ProductColors::firstOrCreate(
                ['colors' => $variant['color']]
            );

                ProductVariants::create([
                    'product_id' => $product->id,
                    'colors_id' => $color->id,
                    'sizes_id' => $size->id,
                    'stock_quantity' => $variant['stock_quantity']
                ]);
            }
        }

        return response()->json(['message' => 'Product updated successfully', 'product' => $product]);
    }

    public function destroy($id) {

        $product = Products::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully!']);
    }

    public function imageUpdate (Request $request, $product_id) {

        $validator = Validator::make($request->all(), [
            'images'   => 'sometimes|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif|max:2048' // Validate each file
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Find the product
        $product = Products::find($product_id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
    
        // Get existing images and delete them from storage
        $existingImages = ProductImages::where('product_id', $product_id)->get();
        foreach ($existingImages as $image) {
            $imagePath = public_path($image->image_url);
            if (file_exists($imagePath)) {
                unlink($imagePath); // Delete the old image file
            }
        }
    
        // Delete existing image records from DB
        ProductImages::where('product_id', $product_id)->delete();
    
        $imageData = [];
        // Upload and store new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $imagePath = 'images/' . $imageName;
                
                $newImage = ProductImages::create([
                    'product_id' => $product->id,
                    'image_url'  => $imagePath
                ]);
        
                // DEBUG: Check if insertion was successful
                if ($newImage) {
                    $imageData[] = $imagePath;
                } else {
                    return response()->json(['error' => 'Failed to insert new images'], 500);
                }
        
                //$imageData[] = $imagePath;
            }
        }
        return response()->json([
            'message' => 'Product images updated successfully',
            'images'  => $imageData
        ]);
    }

    public function delete_images(Request $request, $id) {

        $product = Products::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $oldImages = ProductImages::where('product_id', $id)->get();

        $oldImages->delete();
    }

    public function imageAdd (Request $request, $product_id) {

        $validator = Validator::make($request->all(), [
            'images'   => 'sometimes|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif|max:2048' // Validate each file
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Find the product
        $product = Products::find($product_id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
    
        $imageData = [];
        // Upload and store new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $imagePath = 'images/' . $imageName;
                
                $newImage = ProductImages::create([
                    'product_id' => $product_id,
                    'image_url'  => $imagePath
                ]);
        
                // DEBUG: Check if insertion was successful
                if ($newImage) {
                    $imageData[] = $imagePath;
                } else {
                    return response()->json(['error' => 'Failed to insert new images'], 500);
                }
        
                //$imageData[] = $imagePath;
            }
        }
        return response()->json([
            'message' => 'Product images added successfully',
            'images'  => $imageData
        ]);
    }

    public function updateVariants(Request $request, $id) { 

        $colorIds = ProductVariants::where('product_id', $id)->pluck('colors_id');

        ProductColors::whereIn('id', $colorIds)->update(['colors' => $request->color_name]);
        ///some more change
        return response()->json(['message' => 'Colors updated successfully']);
    }
    
}

