<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\TrafficController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FirewallController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\DatabaseManagerController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('domains', DomainController::class);
    Route::post('/domains/{id}/toggle', [DomainController::class, 'toggle']);

    Route::get('/traffic', [TrafficController::class, 'index']);
    Route::post('/traffic/clear', [TrafficController::class, 'clear'])->name('traffic.clear');

    Route::get('/security', [SecurityController::class, 'index']);
    Route::post('/security/clear', [SecurityController::class, 'clear'])->name('security.clear');

    Route::get('/firewall', [FirewallController::class, 'index']);
    Route::post('/firewall/block', [FirewallController::class, 'block']);
    Route::post('/firewall/auto-block', [FirewallController::class, 'autoBlock'])->name('firewall.auto-block');
    Route::delete('/firewall/{id}', [FirewallController::class, 'unblock']);

    Route::get('/logs', [LogController::class, 'index']);

    Route::get('/databases', [DatabaseManagerController::class, 'index'])->name('databases.index');
    Route::post('/databases/{domain}/config', [DatabaseManagerController::class, 'updateConfig'])->name('databases.config');
    Route::get('/databases/{domain}', [DatabaseManagerController::class, 'show'])->name('databases.show');
    Route::get('/databases/{domain}/table/{table}', [DatabaseManagerController::class, 'table'])->name('databases.table');
    Route::get('/databases/{domain}/table/{table}/structure', [DatabaseManagerController::class, 'structure'])->name('databases.structure');
    Route::get('/databases/{domain}/designer', [DatabaseManagerController::class, 'designer'])->name('databases.designer');
    Route::post('/databases/{domain}/query', [DatabaseManagerController::class, 'runQuery'])->name('databases.query');
    Route::post('/databases/{domain}/import', [DatabaseManagerController::class, 'import'])->name('databases.import');
    Route::get('/databases/{domain}/export', [DatabaseManagerController::class, 'export'])->name('databases.export');
    Route::post('/databases/{domain}/table/{table}/row/{id}', [DatabaseManagerController::class, 'updateRow'])->name('databases.row.update');
});

require __DIR__.'/auth.php';
