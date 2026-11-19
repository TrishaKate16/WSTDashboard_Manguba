<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        $result = [];

        foreach ($products as $product) {
            $result[] = $this->productData($product);
        }

        $numbers = [];
        for ($number = 1; $number <= 5; $number++) {
            $numbers[] = $number;
        }

        return response()->json([
            'status' => true,
            'message' => 'Products retrieved successfully.',
            'numbers' => $numbers,
            'data' => $result,
        ], 200);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $slug)
    {
        $product = Product::with('category')
            ->where(function ($query) use ($slug): void {
                $query->where('slug', $slug)
                    ->orWhere('slug', 'like', $slug . '-%');
            })
            ->first();

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Product retrieved successfully.',
            'data' => $this->productData($product),
        ], 200);
    }
    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    private function productData(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $this->formatProductName($product->name),
            'slug' => $product->slug,
            'category' => $product->category?->name,
            'price' => $product->price,
            'quantity' => $product->quantity,
            'status' => $product->status,
        ];
    }

    private function formatProductName(string $name): string
    {
        return ucwords(strtolower(trim($name)));
    }
}
