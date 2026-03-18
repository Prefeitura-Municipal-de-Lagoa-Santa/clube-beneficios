<?php

use App\Http\Controllers\Admin\BenefitController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'ldap.active', 'role:admin|partner_manager'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', function () {
        return \Inertia\Inertia::render('admin/Dashboard');
    })->name('dashboard');

    // Partners
    Route::resource('parceiros', PartnerController::class)
        ->names('partners')
        ->parameters(['parceiros' => 'partner']);

    // Categories
    Route::resource('categorias', CategoryController::class)
        ->names('categories')
        ->parameters(['categorias' => 'category']);

    // Benefits
    Route::resource('beneficios', BenefitController::class)
        ->names('benefits')
        ->parameters(['beneficios' => 'benefit']);

    // Users (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('usuarios', [UserController::class, 'index'])->name('users.index');
        Route::put('usuarios/{user}', [UserController::class, 'update'])->name('users.update');
        Route::get('usuarios/ldap/search', [UserController::class, 'ldapSearch'])->name('users.ldap.search');
        Route::post('usuarios/ldap/import', [UserController::class, 'ldapImport'])->name('users.ldap.import');

        // Roles
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
});
