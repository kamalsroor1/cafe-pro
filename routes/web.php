<?php

use App\Livewire\Auth\Login;
use App\Livewire\Dashboard\Index as Dashboard;
use App\Livewire\Expenses\ExpenseList;
use App\Livewire\Inventory\IngredientList;
use App\Livewire\Orders\KitchenDisplay;
use App\Livewire\Orders\OrderDetail;
use App\Livewire\Orders\OrderList;
use App\Livewire\Pos\Terminal;
use App\Livewire\Products\ProductList;
use App\Livewire\Reports\ProfitReport;
use App\Livewire\Reports\ShiftReport;
use App\Livewire\Shifts\ShiftManager;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class)->name('login');



Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');

    Route::post('/logout', function () {
        auth()->logout();

        return redirect()->route('login');
    })->name('logout');


    Route::get('/sw.js', function () {
        return response()->file(public_path('sw.js'), [
            'Content-Type' => 'application/javascript',
            'Service-Worker-Allowed' => '/',
        ]);
    })->name('sw');

    Route::middleware('permission:manage products')->group(function () {
        Route::get('/products', ProductList::class)->name('products.index');
    });

    Route::middleware('permission:manage ingredients')->group(function () {
        Route::get('/inventory', IngredientList::class)->name('inventory.index');
    });

    Route::middleware('permission:access pos')->group(function () {
        Route::middleware('shift.open')->group(function () {
            Route::get('/pos', Terminal::class)->name('pos.index');
        });
        Route::get('/shifts', ShiftManager::class)->name('shifts.index');

        Route::get('/orders', OrderList::class)->name('orders.index');
        Route::get('/orders/{order}', OrderDetail::class)->name('orders.show');
        Route::get('/orders/{order}/receipt', [\App\Http\Controllers\InvoiceController::class, 'download'])->name('orders.receipt');
    });

    Route::middleware('permission:view kds')->group(function () {
        Route::get('/kds', KitchenDisplay::class)->name('kds.index');
    });

    Route::middleware('permission:manage expenses')->group(function () {
        Route::get('/expenses', ExpenseList::class)->name('expenses.index');
    });

    Route::middleware('permission:view reports')->group(function () {
        Route::get('/reports/profit', ProfitReport::class)->name('reports.profit');
        Route::get('/reports/shifts', ShiftReport::class)->name('reports.shifts');
    });
});
