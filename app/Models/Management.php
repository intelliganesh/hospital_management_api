<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Management extends Model
{
    protected $table = "managements";
    protected $fillable = [
        "management_name",
        "description",
        "department_type",
        "is_active",
    ];


    public static $columns = [
        "id",
        "management_name",
        "description",
        "department_type",
        "is_active",
    ];

    public static $filter = [
        "management_name",
        "description",
        "department_type",
        "is_active",
    ];

    public static $listcolumns = [
        "id",
        "management_name",
        "description",
        "department_type",
    ];
}
