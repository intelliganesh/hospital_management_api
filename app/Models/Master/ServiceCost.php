<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ServiceCost extends Model
{
    protected $table = 'service_cost';

    protected static function booted()
    {
        static::addGlobalScope('orderByCreatedAt', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }

    protected $fillable = [
        "cost",
        "status",
        "case_type",
        "description",
        "service_name",
        "department_type",
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $columns = ["id", "cost", "case_type", "status", "service_name", "department_type"];
    public static $listcolumns = ["id", "cost", "description", "service_name"];
}
