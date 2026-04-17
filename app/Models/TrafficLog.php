<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrafficLog extends Model
{
    protected $fillable = [
        'domain_id',
        'domain',
        'request_path',
        'status_code',
        'ip',
        'country',
        'country_code',
        'is_bot',
        'user_agent',
        'type',
        'threat',
        'browser',
        'device',
        'session_id',
        'source',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }
}
