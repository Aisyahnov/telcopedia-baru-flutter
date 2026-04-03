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
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');

// Category Explorer
Route::get('/categories', [CategoryController::class, 'index'])->name('category.index');
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('category.show');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware([\App\Http\Middleware\CheckRole::class.':buyer,seller'])->group(function () {
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
        });

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::put('/update', [ProfileController::class, 'update'])->name('update');
            Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        });

        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/', [ChatController::class, 'index'])->name('index');
            Route::get('/start/{product}', [ChatController::class, 'startChat'])->name('start');
            Route::get('/{chat}', [ChatController::class, 'room'])->name('room');
            Route::get('/{chat}/messages', [ChatController::class, 'getNewMessages'])->name('messages');
            Route::post('/{chat}/send', [ChatController::class, 'send'])->name('send');
            Route::put('/message/{message}', [ChatController::class, 'updateMessage'])->name('message.update');
            Route::delete('/message/{message}', [ChatController::class, 'deleteMessage'])->name('message.delete');
        });

        Route::get('/vouchers', [VoucherController::class, 'index'])->name('vouchers.index');
    });
});

Route::middleware(['auth', \App\Http\Middleware\CheckRole::class.':seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::get('/orders', [SellerController::class, 'orders'])->name('orders.index');
    Route::put('/orders/{orderId}/tracking', [SellerController::class, 'updateTracking'])->name('orders.tracking');
});

Route::middleware(['auth', \App\Http\Middleware\CheckRole::class.':admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::delete('/products/{id}', [AdminController::class, 'destroyProduct'])->name('products.destroy');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/vouchers', [AdminController::class, 'vouchers'])->name('vouchers');
    Route::post('/vouchers', [AdminController::class, 'storeVoucher'])->name('vouchers.store');

    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    Route::post('/payments/{id}/approve', [AdminController::class, 'approvePayment'])->name('payments.approve');
    Route::post('/payments/{id}/reject', [AdminController::class, 'rejectPayment'])->name('payments.reject');
});
