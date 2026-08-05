<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Prakriti extends Model
{
    protected $table = "prakriti";
    protected $fillable = [
        "name",
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
