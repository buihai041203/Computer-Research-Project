<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseAudit extends Model
{
protected $fillable = [
'user_id','site_name','action','query_text','ip'
];
}
