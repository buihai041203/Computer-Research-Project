<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'max_domains',
        'stripe_plan_id',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
