<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SampleController;
 
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [CategoryController::class, 'dashboard'])->name('dashboard');
 
Route::get('/sample', [SampleController::class, 'index']);
Route::get('/read', [SampleController::class, 'read']);
Route::get('/greet/{name}', [SampleController::class, 'greet']);