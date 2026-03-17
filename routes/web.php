<?php

use App\Http\Controllers\BenefitController;
use App\Http\Controllers\MemberCardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/verificar/{token}', [VerificationController::class, 'verify'])->name('verification.verify');

// Authenticated routes (servidor)
Route::middleware(['auth', 'ldap.active'])->group(function () {
    Route::get('/beneficios', [BenefitController::class, 'index'])->name('benefits.index');
    Route::get('/beneficios/parceiro/{partner}', [BenefitController::class, 'show'])->name('benefits.show');
    Route::get('/carteirinha', [MemberCardController::class, 'show'])->name('card.show');
    Route::post('/carteirinha', [MemberCardController::class, 'store'])->name('card.store');
});

// Admin routes
require __DIR__.'/admin.php';
