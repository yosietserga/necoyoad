<?php

use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\StorefrontController;
use App\Livewire\Storefront\CartDrawer;
use App\Livewire\Storefront\CheckoutForm;
use App\Livewire\Storefront\ProductPage;
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

// Contact form submission (used by the contact-form widget)
Route::post('/contact/submit', [StorefrontController::class, 'contactSubmit'])->name('contact.submit');

// Async widget rendering (v3 §8 — allows widgets to load via AJAX)
Route::get('/widget/async/{name}', [\App\Http\Controllers\WidgetController::class, 'async'])->name('widget.async');

// Admin FileManager API (auth-protected, audit-logged)
Route::middleware(['auth', 'can:file-manager'])->prefix('admin/api/filemanager')->group(function () {
    Route::get('directories', [\App\Http\Controllers\Admin\FileManagerController::class, 'directories']);
    Route::get('files', [\App\Http\Controllers\Admin\FileManagerController::class, 'files']);
    Route::post('directory', [\App\Http\Controllers\Admin\FileManagerController::class, 'createDirectory']);
    Route::delete('file', [\App\Http\Controllers\Admin\FileManagerController::class, 'deleteFile']);
    Route::delete('directory', [\App\Http\Controllers\Admin\FileManagerController::class, 'deleteDirectory']);
    Route::post('move', [\App\Http\Controllers\Admin\FileManagerController::class, 'move']);
    Route::post('copy', [\App\Http\Controllers\Admin\FileManagerController::class, 'copy']);
    Route::post('rename', [\App\Http\Controllers\Admin\FileManagerController::class, 'rename']);
    Route::post('upload', [\App\Http\Controllers\Admin\FileManagerController::class, 'upload']);
    Route::get('thumbnail', [\App\Http\Controllers\Admin\FileManagerController::class, 'thumbnail']);
});

// Admin ThemeEditor API (auth-protected, audit-logged)
Route::middleware(['auth', 'can:theme-edit'])->prefix('admin/api/theme')->group(function () {
    Route::get('files', [\App\Http\Controllers\Admin\ThemeEditorController::class, 'files']);
    Route::get('file', [\App\Http\Controllers\Admin\ThemeEditorController::class, 'read']);
    Route::post('file', [\App\Http\Controllers\Admin\ThemeEditorController::class, 'save']);
    Route::get('versions', [\App\Http\Controllers\Admin\ThemeEditorController::class, 'versions']);
    Route::post('restore', [\App\Http\Controllers\Admin\ThemeEditorController::class, 'restore']);
});

// Banner event API (receives frontend banner events — slide changes, interactions)
// Rate-limited to prevent abuse; no auth required (events fire for all visitors)
Route::middleware('throttle:120,1')->prefix('api/banner/event')->group(function () {
    Route::post('slide-changed', [\App\Http\Controllers\BannerEventController::class, 'slideChanged']);
    Route::post('interaction', [\App\Http\Controllers\BannerEventController::class, 'interaction']);
});

// Healthcheck: bootstrap/app.php registers '/up' via withRouting(health: '/up')
// which serves Laravel's built-in health route. No explicit route needed here.
