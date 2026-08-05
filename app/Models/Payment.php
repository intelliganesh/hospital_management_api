<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'payment';

    protected $fillable = [
        'ipd_id',
        'amount',
        'doctor_id',
        'amount_for',
        'patient_id',
        'additional_amount_reason',
        'currency',
        // 'payment_type',
        // 'payment_number',
        'appointment_id',
        'consultation_id',
        'front_desk_user_id',
        'include_in_invoice',
        'discount_percentage',
        'discount_amount',
        'payment_status',
        'payment_date',
        // Snapshot fields for patient
        'patient_name',
        'patient_email',
        'patient_phone',
        "patient_number",

        // Snapshot fields for doctor
        'doctor_name',
        'doctor_email',
        'doctor_phone',

        // Snapshot fields for front desk user
        'front_desk_user_name',
        'front_desk_user_email',
        'front_desk_user_phone',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $paymentColumns = [
        'ipd_id',
        'amount',
        'doctor_id',
        'amount_for',
        'currency',
        'patient_id',
        'payment_type',
        'additional_amount_reason',
        'payment_number',
        'appointment_id',
        'front_desk_user_id',

        // Snapshot fields for patient
        'patient_name',
        'patient_email',
        'patient_phone',
        "patient_number",

        // Snapshot fields for doctor
        'doctor_name',
        'doctor_email',
        'doctor_phone',

        // Snapshot fields for front desk user
        'front_desk_user_name',
        'front_desk_user_email',
        'front_desk_user_phone',
    ];
    public static $paymentValidationValues = [
        'ipd_id',
        'amount',
        'currency',
        'doctor_id',
        'amount_for',
        'patient_id',
        'payment_type',
        'payment_number',
        'appointment_id',
        'front_desk_user_id',
    ];
}
