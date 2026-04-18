<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Products\ProductList;
use App\Livewire\Dashboard\Index as Dashboard;

Route::get('/login', Login::class)->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');

    Route::post('/logout', function () {
        auth()->logout();
        return redirect()->route('login');
    })->name('logout');

    Route::middleware('permission:manage products')->group(function () {
        Route::get('/products', ProductList::class)->name('products.index');
    });

    Route::get('/pos', function () {
        return view('layouts.pos');
    })->name('pos');
});
