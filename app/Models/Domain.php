<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SiteDatabase;

class Domain extends Model
{
    protected $fillable = [
        'domain',
        'status',
        'agent_key',
        'root_path',
        'php_version',
    ];

    public function databaseConfig()
    {
        return $this->hasOne(SiteDatabase::class, 'domain_id');
    }
}
