<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Products\ProductList;
use App\Livewire\Inventory\IngredientList;
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

    Route::middleware('permission:manage ingredients')->group(function () {
        Route::get('/inventory', IngredientList::class)->name('inventory.index');
    });

    Route::get('/pos', function () {
        return view('layouts.pos');
    })->name('pos');
});
