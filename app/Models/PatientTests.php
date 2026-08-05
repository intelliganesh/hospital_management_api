<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PatientTests extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'patient_tests';

    protected $fillable = [
        'test_id',
        'test_name',
        'doctor_id',
        'patient_id',
        'test_place',
        'result_status',
        'billing_amount',
        'consultation_id',
        'document_upload',
        'test_description',
        'result_uploaded_by',


        // Snapshot fields for patient
        'patient_name',
        'patient_email',
        'patient_phone',
        "patient_number",

        // Snapshot fields for doctor
        'doctor_name',
        'doctor_email',
        'doctor_phone',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

}
