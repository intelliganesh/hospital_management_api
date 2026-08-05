<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OPD extends Model
{
    use HasFactory, HasUuids;

    protected $table = "opd";
    protected $fillable = [
        'opd_number',
        'patient_id',
        'appointment_id',
        'status',
        'visit_date',
        'complaint',
        'referred_to_doctor_id',
        'converted_to_ipd_id',
    ];

    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = [
        'visit_date' => 'datetime',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }
    public function appointment()
    {
        return $this->belongsTo(Appointments::class, 'appointment_id');
    }
}
