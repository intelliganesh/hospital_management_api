<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ChiefComplaint extends Model
{

    protected $table = "chief_complaint";

    protected static function booted()
    {
        static::addGlobalScope('orderByCreatedAt', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }

    protected $fillable = [
        "is_active",
        "description",
        "complaint_name",
        "department_type",
    ];


    public static $columns = [
        "id",
        "is_active",
        "complaint_name",
        "department_type",
    ];

    public static $listcolumns = [
        "id",
        "description",
        "complaint_name",
    ];
}
