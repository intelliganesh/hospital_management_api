<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class YogaAsana extends Model
{
    protected $table = "yoga_asana";
    protected $fillable = [
        "status",
        "benefits",
        "asana_name",
        "description",
        "difficulty_level",
        "contraindications",
        "recommended_duration",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public static $columns = [
        'id',
        "asana_name",
        "difficulty_level",
        "status"
    ];
}
