<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use App\Models\Domain;

class SiteDatabase extends Model
{
    protected $fillable = [
        'domain_id',
        'site_name',
        'db_connection',
        'db_host',
        'db_port',
        'db_name',
        'db_user',
        'db_password',
        'is_active',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }

    public function setDbPasswordAttribute($value): void
    {
        $this->attributes['db_password'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getDbPasswordAttribute($value): ?string
    {
        if (!$value) return null;
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
