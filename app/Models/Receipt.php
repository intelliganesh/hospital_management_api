<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model
{
    use HasFactory, HasUuids;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    protected static function booted()
    {
        static::addGlobalScope('orderByCreatedAt', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });

        static::creating(function (self $model) {
            if (empty($model->receipt_number)) {
                $lastReceipt = self::withoutGlobalScopes()
                    ->orderBy('receipt_number', 'desc')
                    ->lockForUpdate()
                    ->first();

                $nextNumber = 1;
                if ($lastReceipt && !empty($lastReceipt->receipt_number)) {
                    $nextNumber = $lastReceipt->receipt_number + 1;
                }

                $model->receipt_number = $nextNumber;
            }
        });
    }
    
    protected $table = 'receipts';
    protected $fillable = [
        'invoice_id',
        'amount',
        'currency',
        'date',
        'receipt_number',
        'payment_type',
        'transaction_id',
        'status',
        'notes',
    ];

    public static $columns = [
        'invoice_id',
        'amount',
        'currency',
        'date',
        'receipt_number',
        'payment_type',
        'transaction_id',
        'status',
        'notes',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $hidden = [
        'updated_at',
    ];
}