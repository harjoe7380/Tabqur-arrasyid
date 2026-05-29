<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ParticipantDashboardController;
use App\Http\Controllers\ParticipantTransactionController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Home is overridden by authenticated method, but just in case
Route::get('/home', function() {
    return redirect('/');
})->name('home');

Route::middleware(['auth'])->group(function () {
    
    

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function() {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/report', [AdminController::class, 'report'])->name('report');
        Route::get('/participants', [AdminController::class, 'participants'])->name('participants.index');
        Route::post('/participants', [AdminController::class, 'storeParticipant'])->name('participants.store');
        Route::put('/participants/{id}', [AdminController::class, 'updateParticipant'])->name('participants.update');
        Route::delete('/participants/{id}', [AdminController::class, 'destroyParticipant'])->name('participants.destroy');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::post('/transactions', [AdminController::class, 'storeTransaction'])->name('transactions.store');
        Route::post('/transactions/{transaction}/verify', [AdminController::class, 'verifyTransaction'])->name('transactions.verify');
        Route::get('/transactions/{id}/receipt/pdf', [ReceiptController::class, 'downloadPdf'])->name('receipt.pdf');
        Route::get('/transactions/{id}/receipt/wa', [ReceiptController::class, 'resendWa'])->name('receipt.wa');
        
        // Group Routes
        Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
        Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
        Route::post('/groups/{group}/assign', [GroupController::class, 'assignParticipant'])->name('groups.assign');
        Route::delete('/groups/participant/{participant}', [GroupController::class, 'removeParticipant'])->name('groups.removeParticipant');
        Route::post('/groups/{group}/price', [GroupController::class, 'updatePrice'])->name('groups.updatePrice');
    });

    // Peserta Routes
    Route::middleware(['role:peserta'])->prefix('peserta')->name('peserta.')->group(function() {
        Route::get('/dashboard', [ParticipantDashboardController::class, 'index'])->name('dashboard');
        Route::post('/transactions', [ParticipantTransactionController::class, 'store'])->name('transactions.store');
    });
});
