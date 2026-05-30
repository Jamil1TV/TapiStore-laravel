<?php

require_once app_path('helpers.php');

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/products', [StorefrontController::class, 'products'])->name('products.index');
Route::get('/products/{id}', [StorefrontController::class, 'productById'])->whereNumber('id')->name('products.show');
Route::post('/products/{id}/reviews', [StorefrontController::class, 'storeReview'])->whereNumber('id')->name('products.reviews.store');

Route::get('/pages/products.php', [StorefrontController::class, 'products'])->name('legacy.products');
Route::get('/pages/product.php', [StorefrontController::class, 'product'])->name('legacy.product');
Route::post('/pages/product.php', [StorefrontController::class, 'storeReview'])->name('legacy.product.review');
Route::match(['get', 'post'], '/pages/contact.php', [StorefrontController::class, 'contact'])->name('legacy.contact');
Route::get('/pages/search.php', [StorefrontController::class, 'search'])->name('legacy.search');

Route::match(['get', 'post'], '/pages/login.php', [AuthController::class, 'login'])->name('login');
Route::match(['get', 'post'], '/pages/register.php', [AuthController::class, 'register'])->name('register');
Route::match(['get', 'post'], '/pages/forgot_password.php', [AuthController::class, 'forgotPassword'])->name('password.request');
Route::match(['get', 'post'], '/pages/reset_password.php', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::get('/pages/logout.php', [AuthController::class, 'logout'])->name('logout');

Route::match(['get', 'post'], '/pages/cart.php', [CartController::class, 'index'])->name('legacy.cart');
Route::post('/pages/cart_actions.php', [CartController::class, 'actions'])->name('legacy.cart.actions');

Route::middleware('legacy.auth')->group(function () {
    Route::match(['get', 'post'], '/pages/profile.php', [StorefrontController::class, 'profile'])->name('legacy.profile');
    Route::get('/pages/orders.php', [StorefrontController::class, 'orders'])->name('legacy.orders');
    Route::get('/pages/wishlist.php', [StorefrontController::class, 'wishlist'])->name('legacy.wishlist');
    Route::match(['get', 'post'], '/pages/checkout.php', [CheckoutController::class, 'checkout'])->name('legacy.checkout');
    Route::get('/pages/order_confirmation.php', [CheckoutController::class, 'confirmation'])->name('legacy.confirmation');
});

Route::post('/pages/wishlist_actions.php', [StorefrontController::class, 'wishlistActions'])->name('legacy.wishlist.actions');

Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/index.php', [AdminController::class, 'dashboard'])->name('admin.dashboard.legacy');
    Route::match(['get', 'post'], '/products.php', [AdminController::class, 'products'])->name('admin.products');
    Route::match(['get', 'post'], '/categories.php', [AdminController::class, 'categories'])->name('admin.categories');
    Route::match(['get', 'post'], '/orders.php', [AdminController::class, 'orders'])->name('admin.orders');
    Route::match(['get', 'post'], '/users.php', [AdminController::class, 'users'])->name('admin.users');
    Route::match(['get', 'post'], '/messages.php', [AdminController::class, 'messages'])->name('admin.messages');
});