<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vital extends Model
{
    use HasFactory, HasUuids;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        "bp",
        "cvs",
        "rs",
        "pulse",
        'removed',
        "temperature",
        "consultation_id",
    ];
    protected $hidden = [
        "created_at",
        "updated_at",
    ];
    public static $vitalValidationColumns = ["consultation_id", "temperature", "bp", "pulse", "cvs", "rs"];
    public static $vitalColumns = ["consultation_id", "temperature", "bp", "pulse", "cvs", "rs", "medical_id"];
}
