<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class IPDPreliminaryNotes extends Model
{
    use HasUuids;

    protected $table = 'ipd_preliminary_notes';

    protected $fillable = [
        'id',
        'ipd_id',
        'chief_complaint',
        'associated_complaint',
        'previous_treatment_history',
        'medical_history',
        'family_history',
        'personal_history',
        'allergy',
        'bp',
        'pulse',
        'temperature',
        'spo2',
        'weight',
        'height',
        'cvs',
        'rs',
        'per_abdomen',
        'local_examination',
        'pr',
        'dre',
        'proctoscopy',
        'investigation',
        'hb',
        'tc',
        'esr',
        'rbs',
        'bt',
        'ct',
        'blood_urea',
        'hiv',
        'hbsag',
        'line_of_treatment',
        'provisional_diagnosis',
        'final_diagnosis',
        'treatment_advised',
        'treatment_given',
        'preoperative_instruction',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with IPD
     */
    public function ipd()
    {
        return $this->belongsTo(IPD::class, 'ipd_id', 'id');
    }
}
