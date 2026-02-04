<?php

use Illuminate\Support\Facades\Route;
use Modules\LiveChat\App\Http\Controllers\AdminLiveChatController;
use Modules\LiveChat\App\Http\Controllers\LiveChatController;

Route::prefix('livechat')->name('livechat.')->group(function () {
    // Frontend Routes
    Route::get('/dashboard', [LiveChatController::class, 'index'])->name('index')->middleware('auth');
    Route::post('/start', [LiveChatController::class, 'startChat'])->name('start');
    Route::post('/send', [LiveChatController::class, 'sendMessage'])->name('send');
    Route::get('/messages', [LiveChatController::class, 'getMessages'])->name('messages');
    Route::post('/upload', [LiveChatController::class, 'uploadImage'])->name('upload');
});

Route::prefix('admin/livechat')->middleware(['auth', 'role:admin'])->name('admin.livechat.')->group(function () {
    // Admin Routes
    Route::get('/', [AdminLiveChatController::class, 'index'])->name('index');
    Route::get('/{conversation}', [AdminLiveChatController::class, 'show'])->name('show');
    Route::post('/{conversation}/reply', [AdminLiveChatController::class, 'reply'])->name('reply');
    Route::get('/conversations/poll', [AdminLiveChatController::class, 'pollConversations'])->name('poll');
});
