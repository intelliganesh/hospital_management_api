<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SurgicalHistory extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'surgical_history';

    protected static function booted()
    {
        static::addGlobalScope('orderByCreatedAt', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        "is_active",
        "description",
        "surgery_name",
        "department_type",
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $columns = ['id', "surgery_name", "is_active", "department_type"];
    public static $listcolumns = ['id', "surgery_name", "description"];
}
