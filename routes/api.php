<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrafficController;
use App\Models\TrafficLog;
use App\Http\Controllers\AgentController;


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

    return TrafficLog::selectRaw('country, country_code, count(*) as total')
        ->whereNotNull('country_code')
        ->where('country_code', '!=', '')
        ->groupBy('country', 'country_code')
        ->get();

});

Route::get('/security-events', function(){

    return TrafficLog::whereIn('threat',['HIGH','CRITICAL'])
        ->latest()
        ->limit(20)
        ->get();

});

Route::get('/system-stats', function () {
    // CPU (%): based on 1-min load average against CPU cores
    $load = sys_getloadavg();
    $cpuCores = (int) trim((string) @shell_exec('nproc 2>/dev/null'));
    if ($cpuCores <= 0) {
        $cpuCores = 1;
    }

    $cpu = isset($load[0]) ? min(100, round(($load[0] / $cpuCores) * 100, 1)) : 0;

    // RAM (%): Linux /proc/meminfo (fallback 0)
    $ram = 0;
    if (is_readable('/proc/meminfo')) {
        $mem = @file('/proc/meminfo');
        $map = [];
        foreach ($mem as $line) {
            if (preg_match('/^(\w+):\s+(\d+)/', $line, $m)) {
                $map[$m[1]] = (int) $m[2]; // kB
            }
        }

        if (!empty($map['MemTotal'])) {
            // Prefer MemAvailable if present
            $available = $map['MemAvailable'] ?? (($map['MemFree'] ?? 0) + ($map['Buffers'] ?? 0) + ($map['Cached'] ?? 0));
            $used = max(0, $map['MemTotal'] - $available);
            $ram = round(($used / $map['MemTotal']) * 100, 1);
        }
    }

    return response()->json([
        'cpu' => $cpu,
        'ram' => $ram,
    ]);
});

Route::post('/agent/collect',[AgentController::class,'collect'])->middleware('throttle:120,1');
