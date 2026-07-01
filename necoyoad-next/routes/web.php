<?php

use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\CustomerAuthController;
use App\Livewire\Storefront\CartDrawer;
use App\Livewire\Storefront\ProductPage;
use App\Livewire\Storefront\CheckoutForm;
use Illuminate\Support\Facades\Route;

/**
 * New Necoyoad — Web Routes
 *
 * The storefront uses Livewire 3 + Blade. The admin panel uses Filament 3
 * (registered in a separate routes file by Filament's service provider).
 */

// Home page
Route::get('/', [StorefrontController::class, 'home'])->name('common.home');

// Search
Route::get('/search', [StorefrontController::class, 'search'])->name('search');

// Storefront catalog
Route::get('/products', [StorefrontController::class, 'allProducts'])->name('store.product.all');
Route::get('/product/{product}', [StorefrontController::class, 'product'])->name('store.product');
Route::get('/categories', [StorefrontController::class, 'allCategories'])->name('store.category.all');
Route::get('/category/{category}', [StorefrontController::class, 'category'])->name('store.category');

// CMS
Route::get('/posts', [StorefrontController::class, 'allPosts'])->name('content.post.all');
Route::get('/post/{post}', [StorefrontController::class, 'post'])->name('content.post');
Route::get('/page/{page}', [StorefrontController::class, 'page'])->name('content.page');

// Checkout
Route::get('/checkout', CheckoutForm::class)->name('checkout');

// Customer auth
Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
Route::post('/login', [CustomerAuthController::class, 'login'])->name('customer.login.store');
Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
Route::post('/register', [CustomerAuthController::class, 'register'])->name('customer.register.store');
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

// Marketing tracking endpoints
Route::get('/track/open/{campaign}/{contact}', [StorefrontController::class, 'trackOpen'])->name('marketing.track.open');
Route::get('/track/click/{nonce}', [StorefrontController::class, 'trackClick'])->name('marketing.track.click');

// Unsubscribe
Route::get('/unsubscribe/{token}', [StorefrontController::class, 'unsubscribe'])->name('marketing.unsubscribe');

// Healthcheck: bootstrap/app.php registers '/up' via withRouting(health: '/up')
// which serves Laravel's built-in health route. No explicit route needed here.
