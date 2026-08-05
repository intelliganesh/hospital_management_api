<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddressProof extends Model
{
    protected $table = "user_address_proof";

    protected $fillable = [
        'id_type',
        'consent',
        'user_id',
        'id_number_masked',
        'consent_given_at',
        "id_proof_for_pan",
        'id_number_encrypted',
    ];
}
