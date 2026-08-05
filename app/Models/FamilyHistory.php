<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FamilyHistory extends Model
{
    use HasFactory;


    protected $fillable = [
        'age',
        'name',
        'patient_id',
        'ipd_number',
        'opd_number',
        'relationship',
        'living_status',
        'age_at_death',
        'cause_of_death',
        'additional_notes',
        'documented_by',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

}
