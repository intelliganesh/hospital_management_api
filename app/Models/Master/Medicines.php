<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Medicines extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('orderByCreatedAt', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }

    protected $fillable = [
        'medicine_name',
        'generic_name',
        'dosage_form',
        'strength',
        "department_type",
        // 'strength_value',
        'strength_unit',
        'manufacturer',
        'stock_quantity',
        'unit_price',
        'expiry_date',
        'is_active',
    ];

    protected $casts = [
        'strength_value' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
        'expiry_date' => 'date',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
