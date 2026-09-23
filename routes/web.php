<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EndpointController;
use App\Http\Controllers\Admin\MockRuleController;
use App\Http\Controllers\Admin\RequestLogController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('endpoints', EndpointController::class);
    Route::patch('endpoints/{endpoint}/toggle', [EndpointController::class, 'toggle'])->name('endpoints.toggle');

    Route::get('endpoints/{endpoint}/rules/create', [MockRuleController::class, 'create'])->name('endpoints.rules.create');
    Route::post('endpoints/{endpoint}/rules', [MockRuleController::class, 'store'])->name('endpoints.rules.store');
    Route::get('endpoints/{endpoint}/rules/{rule}/edit', [MockRuleController::class, 'edit'])->name('endpoints.rules.edit');
    Route::put('endpoints/{endpoint}/rules/{rule}', [MockRuleController::class, 'update'])->name('endpoints.rules.update');
    Route::delete('endpoints/{endpoint}/rules/{rule}', [MockRuleController::class, 'destroy'])->name('endpoints.rules.destroy');
    Route::patch('endpoints/{endpoint}/rules/{rule}/toggle', [MockRuleController::class, 'toggle'])->name('endpoints.rules.toggle');

    Route::get('endpoints/{endpoint}/requests', [RequestLogController::class, 'index'])->name('endpoints.requests.index');
    Route::get('endpoints/{endpoint}/requests/{requestLog}', [RequestLogController::class, 'show'])->name('endpoints.requests.show');
});
