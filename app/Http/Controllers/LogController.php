<?php

namespace App\Http\Controllers;

use App\Models\TrafficLog;
use Illuminate\Support\Facades\Http;

class LogController extends Controller
{
    public function index()
    {
        $logs = TrafficLog::latest()->limit(200)->get();

        $logs->transform(function ($log) {
            if (($log->country ?? 'Unknown') !== 'Unknown') {
                return $log;
            }

            $resolvedCountry = $this->resolveCountryForDisplay((string) $log->ip);
            if ($resolvedCountry !== 'Unknown') {
                $log->country = $resolvedCountry;
            }

            return $log;
        });

        return view('logs.index', compact('logs'));
    }

    private function resolveCountryForDisplay(string $ip): string
    {
        try {
            $geo = Http::timeout(2)->get('http://ip-api.com/json/' . $ip)->json();
            if (($geo['status'] ?? null) === 'success' && !empty($geo['country'])) {
                return (string) $geo['country'];
            }
        } catch (\Throwable $e) {
            // fall through
        }

        try {
            $geo = Http::timeout(2)->get('https://ipinfo.io/' . $ip . '/json')->json();
            if (!empty($geo['country'])) {
                return match (strtoupper((string) $geo['country'])) {
                    'VN' => 'Vietnam',
                    'US' => 'United States',
                    'CN' => 'China',
                    'SG' => 'Singapore',
                    'RU' => 'Russia',
                    'DE' => 'Germany',
                    'FR' => 'France',
                    'JP' => 'Japan',
                    'KR' => 'South Korea',
                    'IN' => 'India',
                    'BR' => 'Brazil',
                    'GB' => 'United Kingdom',
                    'NL' => 'Netherlands',
                    'CA' => 'Canada',
                    'AU' => 'Australia',
                    default => 'Unknown',
                };
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return 'Unknown';
    }
}
