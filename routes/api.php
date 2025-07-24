<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\NewsletterSubscriberController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ScooterController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
    Route::get('profile', [AuthController::class, 'profile'])->name('profile')->middleware('auth:sanctum');
});

Route::prefix('scooters')->group(function () {
    Route::get('/', [ScooterController::class, 'index'])->name('scooters.index');
    Route::get('/{scooter}', [ScooterController::class, 'show'])->name('scooters.show');

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/', [ScooterController::class, 'store'])->name('scooters.store');
        Route::put('/{scooter}', [ScooterController::class, 'update'])->name('scooters.update');
        Route::delete('/{scooter}', [ScooterController::class, 'destroy'])->name('scooters.destroy');
    });
});

Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('orders.index')->middleware('auth:sanctum');
    Route::post('/', [OrderController::class, 'store'])->name('orders.store')->middleware('auth:sanctum');
    Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show')->middleware('auth:sanctum');

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::put('/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });
});

Route::prefix('contact-messages')->group(function () {
    Route::get('/', [ContactMessageController::class, 'index'])->name('contact-messages.index')->middleware('auth:sanctum', 'admin');
    Route::post('/', [ContactMessageController::class, 'store'])->name('contact-messages.store');
    Route::get('/{message}', [ContactMessageController::class, 'show'])->name('contact-messages.show')->middleware('auth:sanctum', 'admin');
});

Route::prefix('newsletter')->group(function () {
    Route::post('subscribe', [NewsletterSubscriberController::class, 'subscribe'])->name('newsletter.subscribe');
    Route::post('unsubscribe', [NewsletterSubscriberController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
});

Route::post('webhooks/stripe', [StripeWebhookController::class, 'handle'])->name('webhooks.stripe');
