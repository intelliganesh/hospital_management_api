<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class Test extends Model
{
    // use HasFactory;

    protected $fillable = [
        "test_name",
        "test_number",
        "test_price",
        "tax_price",
        "test_description",
        "department_type",
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $columns = ['id', "department_type", "test_number", "test_price", "tax_price", "test_name"];
}
