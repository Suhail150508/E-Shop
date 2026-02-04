<?php

use Illuminate\Support\Facades\Route;
use Modules\PaymentGateway\App\Http\Controllers\Admin\WalletController as AdminWalletController;
use Modules\PaymentGateway\App\Http\Controllers\Frontend\WalletController;
use Modules\PaymentGateway\App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Payment callback routes
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('{gateway}/success/{order}', [PaymentController::class, 'success'])->name('success');
    Route::get('{gateway}/cancel/{order}', [PaymentController::class, 'cancel'])->name('cancel');
    Route::post('{gateway}/webhook', [PaymentController::class, 'webhook'])->name('webhook');
});

// Admin Wallet Routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('wallet', [AdminWalletController::class, 'index'])->name('wallet.index');
        Route::post('wallet/{user}/status', [AdminWalletController::class, 'updateStatus'])->name('wallet.status');
        Route::get('wallet/transactions', [AdminWalletController::class, 'transactions'])->name('wallet.transactions');
        Route::post('wallet/transactions/{transaction}/approve', [AdminWalletController::class, 'approveTransaction'])->name('wallet.transactions.approve');
        Route::get('wallet/settings', [AdminWalletController::class, 'settings'])->name('wallet.settings');
        Route::put('wallet/settings', [AdminWalletController::class, 'updateSettings'])->name('wallet.settings.update');
    });

// Customer Wallet Routes
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/account/wallet', [WalletController::class, 'index'])->name('customer.wallet.index');
    Route::post('/account/wallet/deposit', [WalletController::class, 'store'])->name('customer.wallet.deposit');
});
