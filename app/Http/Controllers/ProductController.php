<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::paginate(10);
        return ProductResource::collection($products);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'quantity' => $request->quantity,
            'price' => (float) $request->price,
            'description' => $request->description,
        ]
        );
try {
    if ($request->hasFile('photo')) {
        $fileAdders = $product->addMultipleMediaFromRequest(['photo'])
            ->each(function ($fileAdder) {
                $fileAdder->toMediaCollection('photos');
            });
    }
}
        catch(\Exception $e){
            return response()->json(['message' => 'Error in uploading images'], 500);
        }
       
        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return new ProductResource($product);
    }

   
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product ,$id)
    {
        
        $product = Product::findOrFail($id);
       // return $request;
        try{
            $product->update($request->all());
        }
        catch(\Exception $e){
            return response()->json(['message' => 'Error in updating product'], 500);
        }
        
        return new ProductResource($product);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return new ProductResource($product);
    }

    public function filterProducts(Request $request)
    {
        // Get parameters from the request
        $name = $request->input('name');
        $sortBy = $request->input('sort_by'); // 'price_high', 'price_low'
        
        // Start building the query
        $query = Product::query();

        // Add name filter if provided
        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }

        // Add sorting if provided
        if ($sortBy) {
            if ($sortBy == 'price_high') {
                $query->orderBy('price', 'desc');
            } elseif ($sortBy == 'price_low') {
                $query->orderBy('price', 'asc');
            }
        }

        // Execute the query
        $filteredProducts = $query->get();

        return response()->json(['products' => $filteredProducts]);
    }
}
    

