<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DietPlans extends Model
{
    protected $fillable = [
        "diet_name",
        "description",
        "calories",
        "is_active",
        "department_type",
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $columns = [
        'id',
        "diet_name",
        "calories",
        "is_active",
        "department_type"
    ];

    public static $listcolumns = [
        'id',
        "calories",
        "diet_name",
        "description",
    ];
}
