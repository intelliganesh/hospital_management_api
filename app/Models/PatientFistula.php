<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientFistula extends Model
{
    protected $table = 'patient_fistula';

    protected $fillable = [
        'patient_id',
        'patient_number',
        'patient_email',
        'patient_phone',
        'patient_name',
        'no_of_fistula',
        'no_of_tracks_in_one_fistula',
        'no_of_external_opening_position',
        'external_opening_position',
        'internal_opening_position',
        'internal_opening_distance',
        'any_other',
        'no_of_secondary_opening_position',
        'secondary_opening_position',
        'secondary_anal_valve',
        'other_investigation',
        'anal_valve',
        'type_of_crypt',
        'crypt_cause',
        'type_of_fistula_position',
        'type_of_fistula_sphincter',
        'basis_of_high_low_riding',
        'distant_visceral_communication',
        'sono_fistula_gram',
        'mri_fistula_gram',
        'sonologist_findings',
        'fistula_recurrence',
        'fistula_recurrence_surgery_count',
        'fistula_remark',
        'posterior_fistulous_angle',
        'sonologist',
        'created_by',
        'updated_by',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function createdUser(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedUser(){
        return $this->belongsTo(User::class, 'updated_by');
    }
}
