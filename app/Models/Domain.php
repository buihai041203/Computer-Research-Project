<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $fillable = [
        'domain',
        'status',
        'agent_key',
        'root_path',   // Thêm dòng này
        'php_version', // Thêm dòng này
    ];
}
