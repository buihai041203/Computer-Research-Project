<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FirewallController extends Controller
{
    // Lấy danh sách rule hiện tại
    public function getStatus()
    {
        // Nên dùng đường dẫn tuyệt đối /usr/sbin/ufw để tránh lỗi PATH
        $output = shell_exec('sudo /usr/sbin/ufw status numbered');
        return response()->json(['raw_status' => $output]);
    }

    // Chặn một IP
    public function blockIp(Request $request)
    {
        $ip = $request->input('ip'); // Đổi thành input('ip') cho rõ ràng

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            // escapeshellarg để ngăn chặn Command Injection
            $safeIp = escapeshellarg($ip);
            shell_exec("sudo /usr/sbin/ufw deny from $safeIp");
            
            return response()->json(['message' => "Đã chặn IP: $ip"]);
        }

        return response()->json(['message' => "IP không hợp lệ"], 400);
    }

    // Mở chặn IP
    public function unblockIp(Request $request)
    {
        $ip = $request->input('ip');

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            $safeIp = escapeshellarg($ip);
            // Lệnh delete deny giúp xóa chính xác rule đã tạo
            shell_exec("sudo /usr/sbin/ufw delete deny from $safeIp");
            
            return response()->json(['message' => "Đã mở chặn IP: $ip"]);
        }

        return response()->json(['message' => "IP không hợp lệ"], 400);
    }
}
public function getBlockedList()
{
    // Lấy các dòng có chữ DENY trong ufw status
    $output = shell_exec("sudo /usr/sbin/ufw status | grep 'DENY'");
    $ips = [];
    if ($output) {
        preg_match_all('/\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b/', $output, $matches);
        $ips = array_unique($matches[0]);
    }
    return response()->json(['blocked_ips' => $ips]);
}