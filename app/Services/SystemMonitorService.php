<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SystemMonitorController extends Controller
{
    public function stats()
    {
        // 1. Tính toán CPU Load
        // sys_getloadavg() trả về mảng [1min, 5min, 15min]
        $load = sys_getloadavg();
        
        // Để chính xác, ta nên lấy số core thực tế của server thay vì fix cứng số 8
        $numCpus = (int) shell_exec("nproc") ?: 1; 
        $cpuUsage = round(($load[0] / $numCpus) * 100, 2);

        // 2. Tính toán RAM Usage (Dùng lệnh free -m để lấy MB)
        $freeOutput = shell_exec('free -m');
        $lines = explode("\n", trim($freeOutput));
        
        // Dòng thứ 2 chứa thông tin Mem
        // Sử dụng preg_split để xử lý mọi khoảng trắng thừa giữa các số
        $mem = preg_split('/\s+/', $lines[1]);
        
        $totalRam = (float) $mem[1];
        $usedRam  = (float) $mem[2];
        
        $ramUsage = $totalRam > 0 ? round(($usedRam / $totalRam) * 100, 2) : 0;

        return response()->json([
            'cpu' => min($cpuUsage, 100), // Đảm bảo không vượt quá 100%
            'ram' => $ramUsage,
            'details' => [
                'total_ram' => $totalRam . ' MB',
                'used_ram' => $usedRam . ' MB'
            ]
        ]);
    }
}