<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class DRE extends Model
{
    protected $table = '_d_r_e';
    protected static function booted()
    {
        static::addGlobalScope('orderByCreatedAt', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }
    protected $fillable = [
        'id',
        "dre_name",
        "department_type",
        // "internal_opening",
        // "field_induration_at",
        // "hemorrhoid_positions",
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public static $filter = [
        'id',
        "dre_name",
        "department_type",
    ];
    public static $columns = [
        'id',
        "dre_name",
        "department_type",
    ];
    public static $listcolumns = [
        'id',
        "dre_name",
        "department_type",
    ];
}
