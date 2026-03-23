<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedIP extends Model
{
    protected $table = 'blocked_ips';

    protected $fillable = [
        'ip',
        'reason',
    ];
}
