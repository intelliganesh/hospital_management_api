<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConsultationCost extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'consultation_cost';

    protected static function booted()
    {
        static::addGlobalScope('orderByCreatedAt', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }
    protected $fillable = [
        'amount',
        'status',
        "department_type",
        "consultation_name",
    ];
    public static $columns = ['id', "consultation_name", 'amount', 'status', "department_type"];
    public static $listcolumns = ['id', 'consultation_name', 'amount', 'department_type'];
}
