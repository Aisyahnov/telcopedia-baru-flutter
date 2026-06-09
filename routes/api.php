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
use App\Http\Controllers\FileController;

/*
|--------------------------------------------------------------------------
| FILE PROXY (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::get('storage/{path}', [FileController::class, 'serve'])->where('path', '.*');


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
    Route::get('user', [AuthController::class, 'user']);
    Route::put('user/profile', [AuthController::class, 'updateProfile']);
    Route::put('user/password', [AuthController::class, 'updatePassword']);
    Route::post('user/photo', [AuthController::class, 'updatePhoto']);
    Route::post('user/ktm', [AuthController::class, 'updateKtm']);
    Route::post('logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | HOME / PRODUK (BERDASARKAN USER LOGIN)
    |--------------------------------------------------------------------------
    */
    Route::get('home', [ApiHomeController::class, 'index']); 
    Route::get('home/product/{id}', [ApiHomeController::class, 'showProduct']);
    Route::get('categories', [ApiHomeController::class, 'categories']);
    Route::get('home/favorites', [ApiHomeController::class, 'favorites']);
    Route::post('home/favorite/toggle', [ApiHomeController::class, 'toggleFavorite']);
    Route::get('/vouchers', [ApiHomeController::class, 'vouchers']);
    Route::get('/seller/{id}/profile', [ApiHomeController::class, 'sellerProfile']);

    Route::post('/chatbot/send', [\App\Http\Controllers\ChatbotController::class, 'send']);

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
    Route::get('chats', [ApiChatController::class, 'index']);
    Route::get('chat/{chat}', [ApiChatController::class, 'room']);
    Route::get('chat/{chat}/messages', [ApiChatController::class, 'messages']);
    Route::post('chat/{chat}/send', [ApiChatController::class, 'send']);
    Route::put('chat/message/{message}', [ApiChatController::class, 'updateMessage']);
    Route::delete('chat/message/{message}', [ApiChatController::class, 'deleteMessage']);
    Route::post('chat/get-or-create', [ApiChatController::class, 'getOrCreate']);
    /*
    |--------------------------------------------------------------------------
    | ORDERS
    |--------------------------------------------------------------------------
    */
    Route::get('orders', [OrderController::class, 'buyerOrders']);
    Route::post('orders/{order}/complete', [OrderController::class, 'completeOrder']);
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::post('reviews', [OrderController::class, 'storeReview']);
    Route::post('returns', [OrderController::class, 'storeReturn']);

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS
    |--------------------------------------------------------------------------
    */
    Route::get('notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::get('notifications/count', [\App\Http\Controllers\Api\NotificationController::class, 'unreadCount']);
    Route::post('notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    Route::post('notifications/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
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
    Route::post('orders/{id}/approve', [OrderController::class, 'approvePayment']);
    Route::post('orders/{id}/reject', [OrderController::class, 'rejectPayment']);
    Route::put('orders/{id}/tracking', [OrderController::class, 'updateTracking']);
    
    Route::get('returns', [OrderController::class, 'sellerReturns']);
    Route::post('returns/{id}/approve', [OrderController::class, 'approveReturn']);
    Route::post('returns/{id}/reject', [OrderController::class, 'rejectReturn']);

    // Dashboard
    Route::get('dashboard', [SellerDashboardController::class, 'index']);
    Route::get('revenue', [SellerDashboardController::class, 'revenue']);
    Route::get('products/stats', [SellerDashboardController::class, 'productStats']);
    Route::get('chat/list', [SellerDashboardController::class, 'chatList']);
    Route::get('chats', [ApiChatController::class, 'sellerChats']);
    Route::get('reviews', [SellerDashboardController::class, 'reviews']);

    // Penarikan Dana
    Route::get('penarikan', [SellerDashboardController::class, 'penarikan']);
    Route::post('penarikan', [SellerDashboardController::class, 'requestPenarikan']);
});

/*
|--------------------------------------------------------------------------
| ADMIN API (LOGIN + ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', \App\Http\Middleware\CheckRole::class.':admin'])
    ->prefix('admin')
    ->group(function () {

    Route::get('dashboard', [ApiAdminController::class, 'dashboard']);
    
    Route::get('products', [ApiAdminController::class, 'products']);
    Route::post('products/{id}/approve', [ApiAdminController::class, 'approveProduct']);
    Route::post('products/{id}/reject', [ApiAdminController::class, 'rejectProduct']);
    Route::delete('products/{id}', [ApiAdminController::class, 'destroyProduct']);

    Route::get('users', [ApiAdminController::class, 'users']);
    Route::delete('users/{id}', [ApiAdminController::class, 'destroyUser']);

    Route::get('vouchers', [ApiAdminController::class, 'vouchers']);
    Route::post('vouchers', [ApiAdminController::class, 'storeVoucher']);
    Route::put('vouchers/{id}', [ApiAdminController::class, 'updateVoucher']);
    Route::delete('vouchers/{id}', [ApiAdminController::class, 'destroyVoucher']);

    Route::get('payments', [ApiAdminController::class, 'payments']);

    Route::get('penarikan', [ApiAdminController::class, 'penarikan']);
    Route::post('penarikan/{id}/approve', [ApiAdminController::class, 'approvePenarikan']);
    Route::post('penarikan/{id}/reject', [ApiAdminController::class, 'rejectPenarikan']);
});
