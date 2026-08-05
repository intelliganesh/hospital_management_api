<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Avastha extends Model
{
    protected $table = 'avastha';
    protected $fillable = [
        "name"
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
