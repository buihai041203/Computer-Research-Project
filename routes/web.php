<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\TrafficController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FirewallController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\AIController;
use App\Models\TrafficLog;
use App\Http\Controllers\DatabaseManagerController;

// ========================
// PUBLIC
// ========================
Route::get('/', function () {
    return view('welcome');
});

// ========================
// DASHBOARD
// ========================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ========================
// AUTH GROUP
// ========================
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Domains
    Route::resource('domains', DomainController::class);
    Route::post('/domains/{id}/toggle', [DomainController::class, 'toggle']);

    // Traffic
    Route::get('/traffic', [TrafficController::class, 'index']);

    // Security
    Route::get('/security', [SecurityController::class, 'index']);

    // Firewall
    Route::get('/firewall', [FirewallController::class, 'index']);
    Route::post('/firewall/block', [FirewallController::class, 'block']);
    Route::delete('/firewall/{id}', [FirewallController::class, 'unblock']);

    // Logs
    Route::get('/logs', [LogController::class, 'index']);

    // AI CHAT
    Route::post('/api/ai-chat', [AIController::class, 'chat']);

    Route::get('/databases', [DatabaseManagerController::class, 'index'])->name('databases.index');
    Route::post('/databases/{site}/config', [DatabaseManagerController::class, 'updateConfig'])->name('databases.config');
    Route::get('/databases/{site}', [DatabaseManagerController::class, 'show'])->name('databases.show');
    Route::get('/databases/{site}/table/{table}', [DatabaseManagerController::class, 'table'])->name('databases.table');
    Route::post('/databases/{site}/query', [DatabaseManagerController::class, 'runQuery'])->name('databases.query');
});

// ========================
// API (NO AUTH)
// ========================
Route::get('/api/traffic-stats', function () {

    $human = TrafficLog::where('type', 'human')
        ->where('created_at', '>=', now()->subMinute())
        ->count();

    $bot = TrafficLog::where('type', 'bot')
        ->where('created_at', '>=', now()->subMinute())
        ->count();

    return [
        'human' => $human,
        'bot' => $bot
    ];
});

// ========================
require __DIR__.'/auth.php';
