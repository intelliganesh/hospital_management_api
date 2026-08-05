<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fistula extends Model
{

    protected $table = 'fistula';
    protected $fillable = [
        'is_active',
        'description',
        'fistula_name',
        "department_type",
        'sub_fistula_name',
    ];


    public static $columns = [
        "id",
        'is_active',
        'description',
        'fistula_name',
        "department_type",
        'sub_fistula_name',
    ];

    public static $filter = [
        'is_active',
        'description',
        'fistula_name',
        "department_type",
        'sub_fistula_name',
    ];

    public static $listcolumns = [
        "id",
        'description',
        'fistula_name',
        "department_type",
        'sub_fistula_name',
    ];
}
