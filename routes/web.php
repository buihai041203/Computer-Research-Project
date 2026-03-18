<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\TrafficController;
use App\Http\Controllers\SecurityController;
use App\Models\TrafficLog;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FirewallController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\WebsiteController;

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
    Route::post('/domains/{id}/toggle', [DomainController::class, 'toggle'])->name('domains.toggle');
});

Route::get('/traffic',[TrafficController::class,'index'])->middleware('auth');

Route::get('/security',[SecurityController::class,'index'])->middleware('auth');

Route::get('/api/traffic-stats', function () {

    $human = TrafficLog::where('type','human')
        ->where('created_at','>=',now()->subMinute())
        ->count();

    $bot = TrafficLog::where('type','bot')
        ->where('created_at','>=',now()->subMinute())
        ->count();

    return [
        'human'=>$human,
        'bot'=>$bot
    ];

});

Route::get('/databases', function () {
    return view('databases.index');
});

Route::get('/firewall',[FirewallController::class,'index']);
Route::post('/firewall/block',[FirewallController::class,'block']);
Route::delete('/firewall/{id}',[FirewallController::class,'unblock']);

Route::get('/logs',[LogController::class,'index']);

Route::get('/domains',[DomainController::class,'index']);
Route::post('/domains',[DomainController::class,'store']);
// Đường dẫn vào list (bạn đã có sẵn)
Route::get('/domains', [App\Http\Controllers\DomainController::class, 'index']);

// Đường dẫn tạo mới (bạn đã có sẵn)
Route::post('/domains', [App\Http\Controllers\DomainController::class, 'store']);

// THÊM DÒNG NÀY ĐỂ NÚT DELETE HOẠT ĐỘNG
Route::delete('/domains/{id}', [App\Http\Controllers\DomainController::class, 'destroy']);
require __DIR__.'/auth.php';

Route::post('/domains/{id}/toggle', [\App\Http\Controllers\DomainController::class, 'toggle']);

