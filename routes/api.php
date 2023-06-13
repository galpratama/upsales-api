<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\MidtransController;
use App\Http\Controllers\API\TransactionController;
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


// Category API
Route::prefix('category')->name('category.')->group(function () {
    Route::get('', [CategoryController::class, 'fetch'])->name('fetch');
});

// Auth API
Route::name('auth.')->group(function () {
    Route::post('login', [UserController::class, 'login'])->name('login');
    Route::post('register', [UserController::class, 'register'])->name('register');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [UserController::class, 'logout'])->name('logout');
        Route::get('user', [UserController::class, 'fetch'])->name('fetch');
    });
});

// Product API
Route::prefix('product')->middleware('auth:sanctum')->name('product.')->group(function () {
    Route::get('', [ProductController::class, 'fetch'])->name('fetch');
    Route::post('', [ProductController::class, 'create'])->name('create');
    Route::put('{id}', [ProductController::class, 'update'])->name('update');
    Route::delete('{id}', [ProductController::class, 'destroy'])->name('delete');

    // Product Photo API
    Route::prefix('photo')->name('photo.')->group(function () {
        Route::post('', [ProductPhotoController::class, 'create'])->name('create');
        Route::delete('{id}', [ProductPhotoController::class, 'destroy'])->name('delete');
    });
});

// Transaction API
Route::prefix('transaction')->middleware('auth:sanctum')->name('transaction.')->group(function () {
    Route::get('', [TransactionController::class, 'fetch'])->name('fetch');
    Route::post('', [TransactionController::class, 'create'])->name('create');
    Route::delete('{id}', [TransactionController::class, 'destroy'])->name('delete');
});

// Midtrans callback
Route::post('midtrans/notification', [MidtransController::class, 'notification'])->name('midtrans.notification');
