<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AmountFor extends Model
{
    protected $table = 'amount_for';
    protected static function booted()
    {
        static::addGlobalScope('orderByCreatedAt', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }
    protected $fillable = [
        "status",
        "amount_for",
        "description",
    ];
    public static $columns = ["id", "amount_for", "status"];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
