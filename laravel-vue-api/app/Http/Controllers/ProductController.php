<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        // Handle file upload for new products
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $request->merge(['image' => $path]);
        }
        
        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = Product::find($id);

        if ($product) {
            return response()->json($product);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        // Log the entire request content and headers
        Log::info('Update request data:', $request->all());
        Log::info("Update request received for product ID: $id");

        // Find the product by ID
        $product = Product::find($id);

        if ($product) {
            Log::info('Product found: ', $product->toArray());

            // Validate the incoming request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
            ]);

            // Update the product with validated data
            $product->update($validatedData);

            Log::info('Product updated successfully: ', $product->toArray());

            return response()->json($product);
        } else {
            return response()->json(['error' => 'Product not found'], 404);
        }
    }
    public function updateImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('product_images', 'public');
            $product->image = $imagePath;
            $product->save();
        }

        return response()->json($product, 200);
    }


    public function destroy($id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Delete the image if it exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return response()->json(null, 204);
    }
}


