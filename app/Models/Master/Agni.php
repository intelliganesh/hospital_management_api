<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Agni extends Model
{
    protected $table = 'agni';
    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
