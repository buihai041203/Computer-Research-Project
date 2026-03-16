<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityEvent extends Model
{
    protected $fillable = [
        'ip',
        'type',
        'description',
        'attack_type',
        'threat_level',
        'ai_analysis'
    ];
}
