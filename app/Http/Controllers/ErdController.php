<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;

class ErdController extends Controller
{
    public function categories()
    {
        return Category::with('products')->get();
    }

    public function category(Category $category)
    {
        return $category->load('products');
    }

    public function products()
    {
        return Product::with(['category', 'reviews', 'orders'])->get();
    }

    public function product(Product $product)
    {
        return $product->load(['category', 'reviews.user', 'orders.user']);
    }

    public function users()
    {
        return User::with(['orders', 'addresses', 'reviews'])->get();
    }

    public function user(User $user)
    {
        return $user->load(['orders.products', 'addresses', 'reviews.product']);
    }

    public function orders()
    {
        return Order::with(['user', 'products'])->get();
    }

    public function order(Order $order)
    {
        return $order->load(['user', 'products.category']);
    }

    public function addresses()
    {
        return Address::with('user')->get();
    }

    public function address(Address $address)
    {
        return $address->load('user');
    }

    public function reviews()
    {
        return Review::with(['user', 'product'])->get();
    }

    public function review(Review $review)
    {
        return $review->load(['user', 'product']);
    }

    public function orderProducts()
    {
        return OrderProduct::with(['order.user', 'product.category'])->get();
    }
}
