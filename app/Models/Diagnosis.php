<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{

    protected $table = "diagnosis";

    protected $fillable = [
        "icd_code",
        "is_active",
        "description",
        "diagnosis_name",
        "department_type"
    ];
    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public static $columns = [
        "id",
        "icd_code",
        "is_active",
        "diagnosis_name",
        "department_type"
    ];

    public static $listcolumns = [
        "id",
        "description",
        "diagnosis_name",
        "department_type"
    ];
}
