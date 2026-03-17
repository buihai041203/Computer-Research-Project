<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\DatabaseInstance;

class Domain extends Model
{
    protected $fillable = [
        'domain',
        'status',
        'agent_key',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function databaseInstance()
    {
        return $this->hasOne(DatabaseInstance::class);
    }
}
