<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Services\SSLService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProvisionSslJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Domain $domain;

    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    public function handle(): void
    {
        $this->domain->update(['status' => 'ssl_pending']);

        if (! SSLService::requestCertificate($this->domain)) {
            $this->domain->update(['status' => 'ssl_failed']);
            return;
        }

        if (! SSLService::enableHttpsConfig($this->domain)) {
            $this->domain->update(['status' => 'ssl_failed']);
            return;
        }

        $this->domain->update(['status' => 'ssl_active']);
    }
}
