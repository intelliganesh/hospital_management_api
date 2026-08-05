<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConsultationComorbidities extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        "name",
        "is_chronic",
        "description",
        "consultation_id",
        "comorbidities_id",
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $columns = ["id", "consultation_id", "comorbidities_id", "name", "description", "is_cronic", "is_chronic"];
}
