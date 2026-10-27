<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Dashboard Manajemen Transaksi Keuangan
| Bagian: CRUD Customer (langsung ke storage/app/data/customers.csv)
|--------------------------------------------------------------------------
*/

// Redirect root ke endpoint utama
Route::get('/', function () {
    return redirect()->route('dashboard.table');
});

// Endpoint 1: /dashboard-and-tabel
Route::get('/dashboard-and-tabel', [CustomerController::class, 'dashboardAndTable'])->name('dashboard.table');

// Endpoint 2: /create-customer
Route::get('/create-customer', [CustomerController::class, 'create'])->name('customers.create');
Route::post('/create-customer', [CustomerController::class, 'store'])->name('customers.store');

// Endpoint 3: /update-customer
Route::get('/update-customer/{id?}', [CustomerController::class, 'edit'])->name('customers.edit');
Route::post('/update-customer', [CustomerController::class, 'update'])->name('customers.update');
Route::put('/update-customer/{id?}', [CustomerController::class, 'update'])->name('customers.update.put');

// Endpoint Tambahan untuk kelengkapan CRUD: /delete-customer
Route::post('/delete-customer/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
Route::delete('/delete-customer/{id}', [CustomerController::class, 'destroy'])->name('customers.delete');
