<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $fillable = [
        "name",
        "price",
        "status",
        "food_number",
        "tax_price",
        "sub_price",
        "description",
    ];
    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public static $columns = [
        "id",
        "name",
        "price",
        "food_number",
        "status",
        "tax_price",
        "sub_price",
    ];
}
