<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\VisitorController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CardController;

Route::get('/', function () {
    if (auth()->check() && !auth()->user()->isFactoryAccount()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect('/gate');
});

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['vi', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/visitors', [VisitorController::class, 'index'])->name('visitors.index');
    Route::get('/visitors/export', [VisitorController::class, 'export'])->name('visitors.export');
    Route::get('/gate', [VisitorController::class, 'gate'])->name('visitors.gate');
    Route::get('/visitors/{id}', [VisitorController::class, 'show'])->name('visitors.show');
    Route::get('/visitors/{id}/edit', [VisitorController::class, 'edit'])->name('visitors.edit');
    Route::put('/visitors/{id}', [VisitorController::class, 'update'])->name('visitors.update');

    // Admin Route
    Route::get('/admin/dashboard', [\App\Http\Controllers\Web\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/login-history', [\App\Http\Controllers\Web\LoginHistoryController::class, 'index'])->name('admin.login-history');

    // Status Card Route
    Route::get('/cards', [CardController::class, 'index'])->name('cards.index');

    // Truck Gate Routes
    Route::get('/trucks/gate', [\App\Http\Controllers\Web\TruckGateController::class, 'index'])->name('trucks.gate');
    Route::post('/trucks/{truck}/checkout', [\App\Http\Controllers\Web\TruckGateController::class, 'checkout'])->name('trucks.checkout');
    Route::post('/trucks/{truck}/checkin', [\App\Http\Controllers\Web\TruckGateController::class, 'checkin'])->name('trucks.checkin');
    Route::put('/trucks/session/{id}', [\App\Http\Controllers\Web\TruckGateController::class, 'updateSession'])->name('trucks.updateSession');

    // Truck Admin Routes
    Route::get('/admin/trucks', [\App\Http\Controllers\Web\TruckController::class, 'index'])->name('admin.trucks.index');
    Route::post('/admin/trucks', [\App\Http\Controllers\Web\TruckController::class, 'store'])->name('admin.trucks.store');
    Route::put('/admin/trucks/{id}', [\App\Http\Controllers\Web\TruckController::class, 'update'])->name('admin.trucks.update');
    Route::delete('/admin/trucks/{id}', [\App\Http\Controllers\Web\TruckController::class, 'destroy'])->name('admin.trucks.destroy');
    Route::get('/admin/trucks/dashboard', [\App\Http\Controllers\Web\TruckDashboardController::class, 'index'])->name('admin.trucks.dashboard');
    Route::get('/admin/trucks/export', [\App\Http\Controllers\Web\TruckDashboardController::class, 'export'])->name('admin.trucks.export');

    // Driver Admin Routes
    Route::get('/admin/trucks/drivers', [\App\Http\Controllers\Web\DriverController::class, 'index'])->name('admin.drivers.index');
    Route::post('/admin/trucks/drivers', [\App\Http\Controllers\Web\DriverController::class, 'store'])->name('admin.drivers.store');
    Route::put('/admin/trucks/drivers/{id}', [\App\Http\Controllers\Web\DriverController::class, 'update'])->name('admin.drivers.update');
    Route::delete('/admin/trucks/drivers/{id}', [\App\Http\Controllers\Web\DriverController::class, 'destroy'])->name('admin.drivers.destroy');

    // Backdated history registration routes
    Route::post('/admin/visitors/backdated', [\App\Http\Controllers\Web\VisitorController::class, 'storeBackdated'])->name('admin.visitors.backdated');
    Route::post('/admin/trucks/backdated', [\App\Http\Controllers\Web\TruckDashboardController::class, 'storeBackdated'])->name('admin.trucks.backdated');

    // API Routes for Gate Check (Stateful)
    Route::prefix('api')->middleware('api.key')->group(function () {
        Route::get('/session/{barcode}', [\App\Http\Controllers\Api\CheckController::class, 'sessionStatus']);
        Route::post('/check', [\App\Http\Controllers\Api\CheckController::class, 'check']);
    });
});
