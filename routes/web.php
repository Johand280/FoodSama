<?php

use App\Http\Controllers\BusinessSettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\PublicMenuController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');

Route::get('/b/{business:slug}', [PublicMenuController::class, 'index'])->name('public.menu');
Route::get('/b/{business:slug}/pedido', [PublicMenuController::class, 'create'])->name('public.orders.create');
Route::post('/b/{business:slug}/pedido', [PublicMenuController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('public.orders.store');
Route::get('/b/{business:slug}/pedido/{token}', [PublicMenuController::class, 'show'])->name('public.orders.show');

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:5,1');

Route::get('/login', [SessionController::class, 'create'])->name('login');
Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:6,1');
Route::post('/logout', [SessionController::class, 'destroy'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('orders', OrderController::class)->only(['index', 'create', 'store', 'show']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('/orders/{order}/payment', [OrderController::class, 'confirmPayment'])->name('orders.payment');

    Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
    Route::post('/orders/{order}/receipt', [ReceiptController::class, 'store'])->name('receipts.store');
    Route::get('/receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');

    Route::middleware('can:manage-business')->group(function (): void {
        Route::resource('menu', MenuItemController::class)
            ->parameters(['menu' => 'menuItem'])
            ->except(['show']);
        Route::patch('/menu/{menuItem}/availability', [MenuItemController::class, 'toggleAvailability'])
            ->name('menu.availability');

        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');

        Route::get('/profits/export', [ProfitController::class, 'export'])->name('profits.export');
        Route::get('/profits', [ProfitController::class, 'index'])->name('profits.index');

        Route::get('/settings', [BusinessSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [BusinessSettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/employees', [EmployeeController::class, 'store'])->name('settings.employees.store');
    });
});
