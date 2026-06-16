<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProductReturnController;

Route::get('/', function () {
    return redirect()->route('home');
});

Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/product/{id}', [HomeController::class, 'showProduct'])->name('product.show');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/help', [HomeController::class, 'help'])->name('help');
Route::get('/seller/{id}/profile', [HomeController::class, 'sellerProfile'])->name('seller.profile');

Route::post('/chatbot/send', [\App\Http\Controllers\ChatbotController::class, 'send'])->name('chatbot.send');

// Category Explorer
Route::get('/categories', [CategoryController::class, 'index'])->name('category.index');
Route::get('/categories/products/{id}', [CategoryController::class, 'getProductsAjax'])->name('category.products');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware([\App\Http\Middleware\CheckRole::class.':buyer,seller,admin'])->group(function () {
        Route::post('/favorite/toggle', [HomeController::class, 'toggleFavorite'])->name('favorite.toggle');
        Route::get('/favorites', [HomeController::class, 'favorites'])->name('favorites.index');

        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CartController::class, 'index'])->name('index');
            Route::post('/add', [CartController::class, 'add'])->name('add');
            Route::put('/update', [CartController::class, 'update'])->name('update');
            Route::delete('/remove/{itemId}', [CartController::class, 'remove'])->name('remove');
            Route::post('/voucher', [CartController::class, 'applyVoucher'])->name('voucher');
            Route::delete('/voucher', [CartController::class, 'removeVoucher'])->name('voucher.remove');
        });

        Route::prefix('checkout')->name('checkout.')->group(function () {
            Route::get('/', [CheckoutController::class, 'index'])->name('index');
            Route::post('/save', [CheckoutController::class, 'save'])->name('save');
            Route::get('/upload/{orderId}', [CheckoutController::class, 'showUpload'])->name('upload');
            Route::post('/upload/{orderId}', [CheckoutController::class, 'uploadBukti'])->name('upload_bukti');
        });

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::post('/{id}/complete', [OrderController::class, 'complete'])->name('complete');
            Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        });

        Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
        Route::post('/returns', [ProductReturnController::class, 'store'])->name('returns.store');

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::put('/update', [ProfileController::class, 'update'])->name('update');
            Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
            Route::post('/verify', [ProfileController::class, 'verify'])->name('verify');
        });

        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/', [ChatController::class, 'index'])->name('index');
            Route::get('/start/{product}', [ChatController::class, 'startChat'])->name('start');
            Route::get('/start-seller/{seller_id}', [ChatController::class, 'startChatWithSeller'])->name('start_seller');
            Route::get('/{chat}', [ChatController::class, 'room'])->name('room');
            Route::get('/{chat}/messages', [ChatController::class, 'getNewMessages'])->name('messages');
            Route::post('/{chat}/send', [ChatController::class, 'send'])->name('send');
            Route::put('/message/{message}', [ChatController::class, 'updateMessage'])->name('message.update');
            Route::delete('/message/{message}', [ChatController::class, 'deleteMessage'])->name('message.delete');
        });

        Route::get('/vouchers', [VoucherController::class, 'index'])->name('vouchers.index');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Web\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{id}/read', [\App\Http\Controllers\Web\NotificationController::class, 'read'])->name('notifications.read');
        Route::get('/notifications/read-all', [\App\Http\Controllers\Web\NotificationController::class, 'readAll'])->name('notifications.read_all');
    });
});

Route::middleware(['auth', \App\Http\Middleware\CheckRole::class.':seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::get('/orders', [SellerController::class, 'orders'])->name('orders.index');
    Route::put('/orders/{orderId}/tracking', [SellerController::class, 'updateTracking'])->name('orders.tracking');
    Route::get('/returns', [ProductReturnController::class, 'indexSeller'])->name('returns.index');
    Route::post('/returns/{id}/approve', [ProductReturnController::class, 'approve'])->name('returns.approve');
    Route::post('/returns/{id}/reject', [ProductReturnController::class, 'reject'])->name('returns.reject');

    Route::post('/payments/{id}/approve', [SellerController::class, 'approvePayment'])->name('payments.approve');
    Route::post('/payments/{id}/reject', [SellerController::class, 'rejectPayment'])->name('payments.reject');

    Route::get('/penarikan', [\App\Http\Controllers\PenarikanController::class, 'sellerIndex'])->name('penarikan.index');
    Route::post('/penarikan', [\App\Http\Controllers\PenarikanController::class, 'store'])->name('penarikan.store');
    
    Route::get('/chats', [ChatController::class, 'sellerIndex'])->name('chats');
});

Route::middleware(['auth', \App\Http\Middleware\CheckRole::class.':admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::post('/products/{id}/approve', [AdminController::class, 'approveProduct'])->name('products.approve');
    Route::post('/products/{id}/reject', [AdminController::class, 'rejectProduct'])->name('products.reject');
    Route::delete('/products/{id}', [AdminController::class, 'destroyProduct'])->name('products.destroy');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/vouchers', [AdminController::class, 'vouchers'])->name('vouchers');
    Route::post('/vouchers', [AdminController::class, 'storeVoucher'])->name('vouchers.store');
    Route::put('/vouchers/{id}', [AdminController::class, 'updateVoucher'])->name('vouchers.update');
    Route::delete('/vouchers/{id}', [AdminController::class, 'destroyVoucher'])->name('vouchers.destroy');

    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    
    Route::get('/penarikan', [\App\Http\Controllers\PenarikanController::class, 'adminIndex'])->name('penarikan.index');
    Route::post('/penarikan/{id}/approve', [\App\Http\Controllers\PenarikanController::class, 'approve'])->name('penarikan.approve');
    Route::post('/penarikan/{id}/reject', [\App\Http\Controllers\PenarikanController::class, 'reject'])->name('penarikan.reject');
});
