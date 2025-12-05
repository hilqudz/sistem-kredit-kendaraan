<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreditController;

Route::get('/', [CreditController::class, 'index'])->name('dashboard');
Route::post('/submit', [CreditController::class, 'store'])->name('credit.store');
Route::post('/approve/{id}', [CreditController::class, 'approve'])->name('credit.approve');
Route::post('/reject/{id}', [CreditController::class, 'reject'])->name('credit.reject');
Route::get('/application/{id}', [CreditController::class, 'show'])->name('credit.show');

// Health check untuk Railway
Route::get('/health', function () {
    return response()->json(['status' => 'OK', 'timestamp' => now()]);
});