<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrafficLog extends Model
{
    protected $fillable = [
        'domain_id',
        'domain',
        'ip',
        'user_agent',
        'type',
        'country',
        'threat',
        'browser',
        'device',
        'session_id',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }
}
