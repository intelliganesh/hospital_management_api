<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Invoice extends Model
{
    use HasFactory, HasUuids;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected static function booted()
    {
        static::addGlobalScope('orderByCreatedAt', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }
    protected $table = 'invoice';
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'invoice_number',
        'consultation_id',
        'ipd_id',
        'ipd_billing_status',
        // 'payment_type',
        // 'transaction_id',
        'removed',

        'collected_amount',
        'balanced_amount',
        'discount_amount',
        'discount_percentage',

        // Snapshot fields for patient
        'patient_name',
        'patient_email',
        'patient_phone',
        "patient_number",
        // Snapshot fields for doctor
        'doctor_name',
        'doctor_email',
        'doctor_phone',
        "additional_amount_reason",

        'referred_by_name',
        'referred_by_email',
        'referred_by_phone_no',
        'referred_by_hospital_name',
        'comment',
        'currency',
    ];

    public static $columns = [
        'patient_id',
        'doctor_id',
        'consultation_id',
        // 'payment_type',
        // 'transaction_id',

        'collected_amount',
        'balanced_amount',

        // Snapshot fields for patient
        'patient_name',
        'patient_email',
        'patient_phone',
        "patient_number",

        // Snapshot fields for doctor
        'doctor_name',
        'doctor_email',
        'doctor_phone',

        'referred_by_name',
        'referred_by_email',
        'referred_by_phone_no',
        'referred_by_hospital_name',
        'comment',
            'currency',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $hidden = [
        // 'created_at',
        'updated_at',
    ];


    public function receipt()
    {
        return $this->hasMany(Receipt::class);
    }

    public function getPaymentTypeBreakdown()
    {
        return $this->receipt
        ->whereNotNull('payment_type')
        ->groupBy('payment_type')
        ->map(fn ($receipts) => $receipts->sum('amount'))
        ->toArray();
    }
    public function consultation_data()
    {
        return Consultations::where('id', $this->consultation_id)->first();
    }

}
