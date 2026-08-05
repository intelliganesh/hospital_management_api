<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Examination extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        "patient_id",
        "doctor_id",
        "consultation_id",
        "doctor_name",
        "doctor_email",
        "doctor_phone",
        "patient_number",
        "patient_name",
        "patient_email",
        'removed',
        // "temperature",
        // "bp",
        // "pulse",
        // "cvs",
        // "rs",
        "description",
        "appointment_id",
        // "examination_categories_id",
        "examination_overview",
        // "complaint",
        // "advice",
        // "preliminary_diagnosis",
        // "next_visit_date",
    ];

    public static $examinationValidationColumns = [
        'patient_id',
        'doctor_id',
        'description',
        "doctor_name",
        "doctor_email",
        "doctor_phone",
        "patient_name",
        "patient_email",
        "patient_number",
        'appointment_id',
        'consultation_id',
        'examination_overview',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
