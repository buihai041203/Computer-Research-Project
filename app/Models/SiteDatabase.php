<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SiteDatabase extends Model
{
protected $fillable = [
'site_name',
'db_connection',
'db_host',
'db_port',
'db_name',
'db_user',
'db_password',
'is_active',
];

// auto encrypt khi set
public function setDbPasswordAttribute($value): void
{
$this->attributes['db_password'] = $value ? Crypt::encryptString($value) : null;
}

// auto decrypt khi get
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
