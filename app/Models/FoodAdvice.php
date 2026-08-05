<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodAdvice extends Model
{
    protected $fillable = [
        "status",
        "meal_times",
        "advice_text",
        "department_type",
        // "food_description",
    ];
    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public static $columns = [
        "id",
        "status",
        "meal_times",
        "advice_text",
        "department_type"
    ];

    public static $listcolumns = [
        "id",
        "advice_text",
        "meal_times",
    ];
}
