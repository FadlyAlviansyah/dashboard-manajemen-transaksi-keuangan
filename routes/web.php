<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard.table');
});

Route::get('/dashboard-and-tabel', [CustomerController::class, 'dashboardAndTable'])->name('dashboard.table');

Route::get('/create-customer', [CustomerController::class, 'create'])->name('customers.create');
Route::post('/create-customer', [CustomerController::class, 'store'])->name('customers.store');
Route::get('/update-customer/{id?}', [CustomerController::class, 'edit'])->name('customers.edit');
Route::post('/update-customer', [CustomerController::class, 'update'])->name('customers.update');
Route::put('/update-customer/{id?}', [CustomerController::class, 'update'])->name('customers.update.put');
Route::post('/delete-customer/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
Route::delete('/delete-customer/{id}', [CustomerController::class, 'destroy'])->name('customers.delete');

Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction.index');
Route::post('/transaction', [TransactionController::class, 'store'])->name('transaction.store');
Route::delete('/transaction/{id}', [TransactionController::class, 'destroy'])->name('transaction.destroy');
Route::get('/transaction/{id}/edit', [TransactionController::class, 'edit'])->name('transaction.edit');
Route::put('/transaction/{id}', [TransactionController::class, 'update'])->name('transaction.update');
Route::get('/transaction/create', [TransactionController::class, 'create'])->name('transaction.create');
