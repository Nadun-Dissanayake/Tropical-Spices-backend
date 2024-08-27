<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class ProductController extends Controller
{
    public function store(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'product_price' => 'required|numeric|min:0',
            'minimum_quantity' => 'required|integer|min:1',
            'product_description' => 'required|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Limit to 2MB per image
            'images' => 'array|max:5' // Maximum 5 images
        ]);
    
        // If validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Store product in the database
        $product = new Product();
        $product->product_name = $request->input('product_name');
        $product->short_description = $request->input('short_description');
        $product->product_price = $request->input('product_price');
        $product->minimum_quantity = $request->input('minimum_quantity');
        $product->product_description = $request->input('product_description');
        $product->save();
    
        // Handle image upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/products'), $imageName);
                $imagePath = 'uploads/products/' . $imageName;
                
                // Assuming you have a relationship set up for images
                $product->images()->create(['path' => $imagePath]);
            }
        }
    
        return response()->json(['message' => 'Product created successfully!'], 201);
    }
    

    public function ShowProducts()  
    {
        $products = Product::with('images')->get();

        return response()->json($products);
    }

    public function ShowProductById($id)
    {
        $product = Product::with('images')->where('id', $id)->first();
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Delete associated images
        foreach ($product->images as $image) {
            $imagePath = public_path($image->path);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $image->delete();
        }

        // Delete the product
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully!'], 200);
    }

    public function updateProduct(Request $request, $id)
{
    // Find the product
    $product = Product::findOrFail($id);

    // Validation rules
    $rules = [
        'product_name' => 'sometimes|required|string|max:255',
        'short_description' => 'sometimes|required|string|max:500',
        'product_price' => 'sometimes|required|numeric|min:0',
        'minimum_quantity' => 'sometimes|required|integer|min:1',
        'product_description' => 'sometimes|required|string',
        'images.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];

    $request->validate($rules);

    // Update only the fields that are present in the request
    if ($request->has('product_name')) {
        $product->product_name = $request->input('product_name');
    }
    if ($request->has('short_description')) {
        $product->short_description = $request->input('short_description');
    }
    if ($request->has('product_price')) {
        $product->product_price = $request->input('product_price');
    }
    if ($request->has('minimum_quantity')) {
        $product->minimum_quantity = $request->input('minimum_quantity');
    }
    if ($request->has('product_description')) {
        $product->product_description = $request->input('product_description');
    }

    // Handle image replacement if present
    if ($request->hasFile('images')) {
        // Delete old images
        foreach ($product->images as $image) {
            $imagePath = public_path($image->path);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $image->delete();
        }

        // Upload and save new images
        foreach ($request->file('images') as $image) {
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/products'), $imageName);
            $imagePath = 'uploads/products/' . $imageName;

            // Assuming you have a relationship set up for images
            $product->images()->create(['path' => $imagePath]);
        }
    }

    $product->save();

    return response()->json(['message' => 'Product updated successfully!', 'data' => $product], 200);
}




}

