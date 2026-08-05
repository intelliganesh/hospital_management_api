<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OnExaminations extends Model
{

    protected static function booted()
    {
        static::addGlobalScope('orderByCreatedAt', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }

    protected $fillable = [
        "finding",
        "is_active",
        "normal_range",
        "department_type",
        "examination_type",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public static $columns = [
        "id",
        "finding",
        "is_active",
        "normal_range",
        "department_type",
        "examination_type",
    ];
    public static $listcolumns = [
        "id",
        "finding",
        "normal_range",
        "department_type",
        "examination_type",
    ];
}
