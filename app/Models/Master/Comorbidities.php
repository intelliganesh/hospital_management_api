<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Comorbidities extends Model
{

    protected static function booted()
    {
        static::addGlobalScope('orderByCreatedAt', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }

    protected $fillable = [
        "name",
        "is_active",
        // "is_chronic",
        "description",
        "department_type"
    ];


    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $columns = [
        "id",
        "name",
        "is_active",
        // "is_chronic",
        "description",
        "department_type"
    ];

    public static $listcolumns = [
        "id",
        "name",
        "description",
        "department_type"
    ];
}
