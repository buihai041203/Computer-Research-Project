<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Models\TrafficLog;
use App\Models\Visitor;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stevebauman\Location\Facades\Location;

class LogTrafficJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function handle(): void
    {
        $domainModel = Domain::firstOrCreate(
            ['domain' => $this->payload['domain']],
            ['agent_key' => bin2hex(random_bytes(16)), 'status' => 'active']
        );

        $country = 'Unknown';

        try {
            $location = Location::get($this->payload['ip']);
            if ($location && $location->countryName) {
                $country = $location->countryName;
            }
        } catch (\Exception $e) {
            logger()->warning('LogTrafficJob: geo lookup failed', ['ip' => $this->payload['ip'], 'error' => $e->getMessage()]);
        }

        TrafficLog::create([
            'domain_id' => $domainModel->id,
            'domain' => $this->payload['domain'],
            'ip' => $this->payload['ip'],
            'user_agent' => $this->payload['user_agent'] ?? 'Unknown',
            'type' => str_contains(strtolower($this->payload['user_agent'] ?? ''), 'bot') ? 'bot' : 'human',
            'country' => $country,
        ]);

        Visitor::create([
            'ip' => $this->payload['ip'],
            'country' => $country,
            'user_agent' => $this->payload['user_agent'] ?? 'Unknown',
            'is_bot' => str_contains(strtolower($this->payload['user_agent'] ?? ''), 'bot'),
        ]);

        AnalyzeSecurityJob::dispatch($this->payload);
    }
}
