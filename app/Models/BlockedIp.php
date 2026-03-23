<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedIP extends Model
{
    // explicit table name to avoid Laravel snake-case pluralization issues
    protected $table = 'blocked_ips';

    protected $fillable = [
        'ip',
        'reason',
    ];
}
