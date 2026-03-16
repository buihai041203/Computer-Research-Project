<?php

namespace App\Services;

use App\Models\TrafficLog;

class AISecurityAnalyzer
{
    public static function analyze(string $ip, ?string $userAgent, int $requestFrequency, string $path): array
    {
        $attackType = 'Normal traffic';
        $threatLevel = 'LOW';
        $explanation = 'Traffic appears normal';

        $botKeywords = ['bot', 'crawl', 'spider', 'slurp', 'scan', 'nmap', 'masscan'];
        $isBot = false;

        if ($userAgent) {
            foreach ($botKeywords as $keyword) {
                if (stripos($userAgent, $keyword) !== false) {
                    $isBot = true;
                    break;
                }
            }
        }

        $suspiciousPaths = ['/admin', '/login', '/wp-login.php', '/xmlrpc.php', '/api/', '/.env'];
        $pathCount = 0;

        foreach ($suspiciousPaths as $suspect) {
            if (str_contains(strtolower($path), strtolower($suspect))) {
                $pathCount++;
            }
        }

        if ($requestFrequency >= 200) {
            $attackType = 'DDoS attempt';
            $threatLevel = 'CRITICAL';
            $explanation = 'Very high request frequency detected in last minute';
        } elseif ($requestFrequency >= 100) {
            $attackType = 'DDoS attempt';
            $threatLevel = 'HIGH';
            $explanation = 'High request frequency (possible DDoS)';
        } elseif ($isBot && $pathCount > 0) {
            $attackType = 'Scanner bot';
            $threatLevel = 'HIGH';
            $explanation = 'Bot user-agent accessing suspicious endpoints';
        } elseif ($isBot) {
            $attackType = 'Bot activity';
            $threatLevel = 'MEDIUM';
            $explanation = 'Bot-like user agent detected';
        } elseif (preg_match('/(union select|select .* from|drop table|or 1=1|admin\(|sleep\(|benchmark\(|\<script\>)/i', $path) || preg_match('/(union select|select .* from|drop table|or 1=1|;--|\<|\>)/i', $userAgent ?? '')) {
            $attackType = 'Possible SQL injection';
            $threatLevel = 'CRITICAL';
            $explanation = 'SQL injection patterns found in request path or user agent';
        } elseif ($requestFrequency >= 50 && $requestFrequency < 100) {
            $attackType = 'Brute force attack';
            $threatLevel = 'HIGH';
            $explanation = 'Moderate request frequency, login-style endpoint scans';
        } elseif ($pathCount > 0) {
            $attackType = 'Possible scanner';
            $threatLevel = 'MEDIUM';
            $explanation = 'Requests to known high-value endpoints';
        }

        return [
            'threat_level' => $threatLevel,
            'attack_type' => $attackType,
            'explanation' => $explanation,
            'metadata' => [
                'ip' => $ip,
                'user_agent' => $userAgent,
                'request_freq' => $requestFrequency,
                'path' => $path
            ]
        ];
    }
}
