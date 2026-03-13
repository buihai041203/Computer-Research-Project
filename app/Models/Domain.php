<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $fillable = [
        'domain',
        'ip',
        'status',
        'agent_key',
        'type'
    ];
}
