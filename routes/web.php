<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Página inicial
Route::get('/', function () {
    return view('welcome');
});

// Dashboard (autenticado)
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | CRUDs principais – Web interface
    |--------------------------------------------------------------------------
    */

    Route::resource('roles', RoleController::class)->except(['show']);
    Route::resource('departments', DepartmentController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('expenses', ExpenseController::class)->except(['show']);
    Route::resource('invoices', InvoiceController::class)->except(['show']);
});
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');

    Route::resource('categories', CategoryController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::resource('products', ProductController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('invoices', InvoiceController::class);
    Route::resource('payrolls', PayrollController::class);
    Route::resource('settings', SettingController::class);

    Route::get('/notifications', [NotificationInternalController::class, 'index'])->name('notifications.index');
});

require __DIR__.'/auth.php';
