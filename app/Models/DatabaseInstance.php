<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseInstance extends Model
{
    protected $table = 'databases';

    protected $fillable = [
        'domain_id',
        'db_name',
        'db_user',
        'db_password',
        'status',
    ];

    protected $hidden = [
        'db_password',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }
}
