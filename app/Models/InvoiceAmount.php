<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceAmount extends Model
{
    use HasFactory, HasUuids;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'invoice_id',
        'payment_type',
        'transaction_id',
        'balanced_amount',
        'collected_amount',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

}
