<?php

namespace App\Models;

use App\Models\Master\Medicines;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prescriptions extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'consultation_id',
        'doctor_id',
        'patient_id',
        'patient_number',
        'patient_name',
        'doctor_name',
        'patient_email',
        'doctor_email',
        'patient_phone',
        'doctor_phone',
        'medicine_ids',
        'medicine_name',
        'dosage',
        'duration',
        'time',
        'food_advice',
    ];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function consultations()
    {
        return $this->belongsTo(Consultations::class, 'consultation_id');
    }

    public function medicines()
    {
        return $this->belongsToMany(Medicines::class, 'medicine_ids');
    }
}
