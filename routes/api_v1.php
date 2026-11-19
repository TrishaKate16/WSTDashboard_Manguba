<?php
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ErdController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Api\V1\ProductController;
 
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('charts', [CategoryController::class, 'chartData'])->name('charts.index');
 
Route::controller(ErdController::class)->group(function (): void {
    Route::get('categories', 'categories')->name('categories.index');
    Route::get('categories/{category}', 'category')->name('categories.show');
 
    Route::get('products/queries', [ProductController::class, 'queries'])->name('products.queries');
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
 
    Route::get('users', 'users')->name('users.index');
    Route::get('users/{user}', 'user')->name('users.show');
 
    Route::get('orders', 'orders')->name('orders.index');   
    Route::get('orders/{order}', 'order')->name('orders.show');
 
    Route::get('addresses', 'addresses')->name('addresses.index');
    Route::get('addresses/{address}', 'address')->name('addresses.show');
 
    Route::get('reviews', 'reviews')->name('reviews.index');
    Route::get('reviews/{review}', 'review')->name('reviews.show');
 
    Route::get('order-products', 'orderProducts')->name('order-products.index');
});