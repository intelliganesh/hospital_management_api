<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Koshta extends Model
{
    protected $table = 'koshta';
    protected $fillable = [
        "name",
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
