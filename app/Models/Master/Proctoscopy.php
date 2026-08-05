<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Proctoscopy extends Model
{

    protected $table = 'proctoscopys';
    protected static function booted()
    {
        static::addGlobalScope('orderByCreatedAt', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }
    protected $fillable = [
        'id',
        "department_type",
        "proctoscopys_name",
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public static $filter = [
        'id',
        "department_type",
        "proctoscopys_name",
    ];
    public static $columns = [
        'id',
        "department_type",
        "proctoscopys_name",
    ];
    public static $listcolumns = [
        'id',
        "department_type",
        "proctoscopys_name",
    ];
}
