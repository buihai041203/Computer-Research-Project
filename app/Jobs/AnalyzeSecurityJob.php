<?php

namespace App\Jobs;

use App\Models\SecurityEvent;
use App\Models\TrafficLog;
use App\Jobs\SendAlertJob;
use App\Services\AISecurityAnalyzer;
use App\Services\FirewallService;
use App\Services\SecurityDetector;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AnalyzeSecurityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function handle(): void
    {
        $ip = $this->payload['ip'];
        $userAgent = $this->payload['user_agent'] ?? null;
        $path = $this->payload['path'] ?? '/';

        $requestFrequency = TrafficLog::where('ip', $ip)
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        $aiResult = AISecurityAnalyzer::analyze($ip, $userAgent, $requestFrequency, $path);

        if (in_array($aiResult['threat_level'], ['HIGH', 'CRITICAL'])) {
            $event = SecurityEvent::create([
                'ip' => $ip,
                'domain' => $this->payload['domain'] ?? null,
                'type' => $aiResult['attack_type'],
                'description' => $aiResult['explanation'],
                'attack_type' => $aiResult['attack_type'],
                'threat_level' => $aiResult['threat_level'],
                'ai_analysis' => json_encode($aiResult['metadata']),
            ]);

            if ($aiResult['threat_level'] === 'CRITICAL') {
                FirewallService::block($ip, 'AI detected threat: '.$aiResult['attack_type']);
            }

            SendAlertJob::dispatch($event->id);
        }

        if (SecurityDetector::detect($ip)) {
            // detect may also create event and block; send alert for this event
            $event = SecurityEvent::latest()->first();
            if ($event) {
                SendAlertJob::dispatch($event->id);
            }
        }
    }
}
