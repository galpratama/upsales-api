<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductPhotoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth API
Route::name('auth.')->group(function () {
    Route::post('login', [UserController::class, 'login'])->name('login');
    Route::post('register', [UserController::class, 'register'])->name('register');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [UserController::class, 'logout'])->name('logout');
        Route::get('user', [UserController::class, 'fetch'])->name('fetch');
    });
});

// Category API
Route::prefix('category')->middleware('auth:sanctum')->name('category.')->group(function () {
    Route::get('', [CategoryController::class, 'fetch'])->name('fetch');
});

// Product API
Route::prefix('product')->middleware('auth:sanctum')->name('product.')->group(function () {
    Route::get('', [ProductController::class, 'fetch'])->name('fetch');
    Route::post('', [ProductController::class, 'create'])->name('create');
    Route::put('{id}', [ProductController::class, 'update'])->name('update');
    Route::delete('{id}', [ProductController::class, 'destroy'])->name('delete');

    // Product Photo API
    Route::prefix('photo')->name('.photo.')->group(function () {
        Route::post('', [ProductPhotoController::class, 'create'])->name('create');
        Route::delete('{id}', [ProductPhotoController::class, 'destroy'])->name('delete');
    });
});
