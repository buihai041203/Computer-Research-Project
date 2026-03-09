<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrafficLog extends Model
{
    protected $fillable = [
    'domain',
    'ip',
    'user_agent',
    'type'
];

public function domain()
{
    return $this->belongsTo(Domain::class);
}
}
