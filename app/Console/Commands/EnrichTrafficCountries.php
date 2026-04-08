<?php

namespace App\Console\Commands;

use App\Models\TrafficLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class EnrichTrafficCountries extends Command
{
    protected $signature = 'traffic:enrich-country {--limit=100}';
    protected $description = 'Fill missing country values for traffic logs without blocking the main import flow';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));

        $rows = TrafficLog::query()
            ->where(function ($q) {
                $q->whereNull('country')->orWhere('country', '')->orWhere('country', 'Unknown')
                  ->orWhereNull('country_code')->orWhere('country_code', '');
            })
            ->orderBy('id')
            ->limit($limit)
            ->get();

        $updated = 0;
        foreach ($rows as $row) {
            if ($this->isSystemIp((string) $row->ip)) {
                $row->country = 'Local';
                $row->country_code = 'LOCAL';
                $row->save();
                $updated++;
                continue;
            }

            $geo = Cache::remember('geo_country_full_' . $row->ip, now()->addHours(24), function () use ($row) {
                try {
                    $data = Http::timeout(3)->get('http://ip-api.com/json/' . $row->ip)->json();
                    return [
                        'country' => $data['country'] ?? 'Unknown',
                        'country_code' => $data['countryCode'] ?? null,
                    ];
                } catch (\Throwable $e) {
                    return [
                        'country' => 'Unknown',
                        'country_code' => null,
                    ];
                }
            });

            $country = $geo['country'] ?? 'Unknown';
            $countryCode = $geo['country_code'] ?? null;

            if ($row->country !== $country || $row->country_code !== $countryCode) {
                $row->country = $country;
                $row->country_code = $countryCode;
                $row->save();
                $updated++;
            }
        }

        $this->info("Updated {$updated} traffic log country values.");
        return self::SUCCESS;
    }

    private function isSystemIp(string $ip): bool
    {
        if (in_array($ip, ['127.0.0.1', '::1'], true)) {
            return true;
        }

        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
}
