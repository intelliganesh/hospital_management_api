<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Vikruti extends Model
{
    protected $table = "vikruti";
    protected $fillable = [
        "name",
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
