<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogActivity extends Model
{
    use HasFactory;
    protected $table = "logs";
    protected $fillable = [
        'subject',
        'log',
        'status_type',
        'status_code',
        'url',
        'method',
        'ip',
        'agent',
        'user_id',
    ];
}
