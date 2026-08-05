<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PatientAttendantAddressProof extends Model
{
    use HasFactory, HasUuids;
    protected $table = "patient_attendant_address_proof";

    protected $fillable = [
        'id_type',
        "image",
        'consent',
        'patient_id',
        'id_number_masked',
        "id_proof_for_pan",
        'consent_given_at',
        'id_number_encrypted',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
