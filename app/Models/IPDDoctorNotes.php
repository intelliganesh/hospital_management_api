<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IPDDoctorNotes extends Model
{
    use HasFactory, HasUuids;
    
    protected $table = 'ipd_doctor_notes';
    
    protected $fillable = [
        'ipd_id',
        'doctor_id',
        'doctor_name',
        'doctor_phone',
        'datetime',
        'gc',
        'bp',
        'pr',
        'clinical_notes',
        'diagnosis',
    ];

    public static $columns = [
        'id',
        'ipd_id',
        'doctor_id',
        'doctor_name',
        'doctor_phone',
        'datetime',
        'gc',
        'bp',
        'pr',
        'clinical_notes',
        'diagnosis',
    ];

    public static $filter = [
        'ipd_id',
        'doctor_id',
        'datetime',
        'gc',
        'bp',
        'pr',
    ];

    public static $listcolumns = [
        'id',
        'ipd_id',
        'doctor_id',
        'doctor_name',
        'doctor_phone',
        'datetime',
        'gc',
        'bp',
        'pr',
        'clinical_notes',
        'diagnosis',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
