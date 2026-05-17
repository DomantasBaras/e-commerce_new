<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\ElasticsearchService;
use Inertia\Inertia;
use App\Models\User;

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
        // Find the product by ID
        $product = Product::find($id);

        if ($product) {
            // Store original data before updating
            $originalData = $product->getOriginal();
      //      Log::info('request data:', ['request' => $request]);
     //       Log::info('Original data:', ['originalData' => $originalData]);
            // Validate the incoming request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
            ]);

            // Update the product
            $product->update($validatedData);

            return response()->json([
                'product' => $product,
                'original' => $originalData,
            ]);
        }

        return response()->json(['error' => 'Product not found'], 404);
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

    protected $elasticsearch;

    public function __construct(ElasticsearchService $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = $this->elasticsearch->search('products', $query);

        return view('products.index', ['products' => $results['hits']['hits']]);
    }

    public function adminIndex()
    {
        return view('admin.products.index');
    }

    public function create()
    {
        return Inertia::render('Admin/ProductForm');
    }

    // Show edit form with product data
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return Inertia::render('Admin/ProductForm', [
            'product' => $product
        ]);
    }

}


