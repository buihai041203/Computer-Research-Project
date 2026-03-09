<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrafficController;
use App\Models\TrafficLog;


Route::get('/traffic-stats', [TrafficController::class, 'stats']);

Route::get('/top-ip', function () {

    return TrafficLog::selectRaw('ip, count(*) as total')
        ->groupBy('ip')
        ->orderByDesc('total')
        ->limit(10)
        ->get();

});

Route::get('/country-stats', function () {

    return TrafficLog::selectRaw('country, count(*) as total')
        ->groupBy('country')
        ->orderByDesc('total')
        ->limit(10)
        ->get();

});

Route::get('/attack-map', function(){

    return TrafficLog::selectRaw('country, count(*) as total')
        ->groupBy('country')
        ->get();

});

Route::get('/security-events', function(){

    return TrafficLog::whereIn('threat',['HIGH','CRITICAL'])
        ->latest()
        ->limit(20)
        ->get();

});
