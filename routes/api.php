<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SellerDashboardController;
use App\Http\Controllers\Api\CartController as ApiCartController;
use App\Http\Controllers\Api\CheckoutController as ApiCheckoutController;
use App\Http\Controllers\Api\ChatController as ApiChatController;
use App\Http\Controllers\Api\HomeController as ApiHomeController;
use App\Http\Controllers\Api\AdminController as ApiAdminController;

/*
|--------------------------------------------------------------------------
| AUTH (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER (SEMUA LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTH
    |--------------------------------------------------------------------------
    */
    Route::post('logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | HOME / PRODUK (BERDASARKAN USER LOGIN)
    |--------------------------------------------------------------------------
    */
    Route::get('home', [ApiHomeController::class, 'index']); 
    Route::get('home/product/{id}', [ApiHomeController::class, 'showProduct']);

    /*
    |--------------------------------------------------------------------------
    | CART (BERDASARKAN ID/NIM USER LOGIN)
    |--------------------------------------------------------------------------
    */
    Route::get('cart', [ApiCartController::class, 'index']);
    Route::post('cart/add', [ApiCartController::class, 'add']);
    Route::put('cart/update', [ApiCartController::class, 'update']);
    Route::delete('cart/remove/{itemId}', [ApiCartController::class, 'remove']);
    Route::post('cart/voucher', [ApiCartController::class, 'applyVoucher']);

    /*
    |--------------------------------------------------------------------------
    | CHECKOUT
    |--------------------------------------------------------------------------
    */
    Route::get('checkout', [ApiCheckoutController::class, 'index']);
    Route::post('checkout/save', [ApiCheckoutController::class, 'save']);
    Route::post('checkout/upload/{order}', [ApiCheckoutController::class, 'uploadBukti']);
    Route::post('checkout/apply-voucher', [ApiCheckoutController::class, 'applyVoucher']);

    /*
    |--------------------------------------------------------------------------
    | CHAT 
    |--------------------------------------------------------------------------
    */
    Route::get('chat/{chat}', [ApiChatController::class, 'room']);
    Route::post('chat/{chat}/send', [ApiChatController::class, 'send']);
    Route::put('chat/message/{message}', [ApiChatController::class, 'updateMessage']);
    Route::delete('chat/message/{message}', [ApiChatController::class, 'deleteMessage']);
});

/*
|--------------------------------------------------------------------------
| SELLER API (LOGIN + ROLE SELLER)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', \App\Http\Middleware\CheckRole::class.':seller'])
    ->prefix('seller')
    ->group(function () {

    // Produk milik seller
    Route::apiResource('products', ProductController::class);

    // Order masuk ke seller
    Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'update']);

    // Dashboard
    Route::get('dashboard', [SellerDashboardController::class, 'index']);
    Route::get('revenue', [SellerDashboardController::class, 'revenue']);
    Route::get('products/stats', [SellerDashboardController::class, 'productStats']);
    Route::get('chat/list', [SellerDashboardController::class, 'chatList']);
});

/*
|--------------------------------------------------------------------------
| ADMIN API (LOGIN + ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', \App\Http\Middleware\CheckRole::class.':admin'])
    ->prefix('admin')
    ->group(function () {

    Route::get('products', [ApiAdminController::class, 'products']);
    Route::delete('products/{id}', [ApiAdminController::class, 'destroyProduct']);

    Route::get('users', [ApiAdminController::class, 'users']);
    Route::delete('users/{id}', [ApiAdminController::class, 'destroyUser']);

    Route::get('vouchers', [ApiAdminController::class, 'vouchers']);
    Route::post('vouchers', [ApiAdminController::class, 'storeVoucher']);
    Route::put('vouchers/{id}', [ApiAdminController::class, 'updateVoucher']);
    Route::delete('vouchers/{id}', [ApiAdminController::class, 'destroyVoucher']);
});
