<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class AmountFor extends Model
{

    protected $table = "amount_for";
    protected $fillable = [
        "status",
        "amount_for",
        "description",
    ];
    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public static $columns = [
        "id",
        "status",
        "amount_for",
    ];
}
