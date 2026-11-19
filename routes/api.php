<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HumanResourceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SampleController;

Route::post('/payroll/calculate', [HumanResourceController::class, 'calculate']);
Route::get('/sample', [SampleController::class, 'index']);
Route::get('/read', [SampleController::class, 'read']);
Route::get('/greet/{name}', [SampleController::class, 'greet']);