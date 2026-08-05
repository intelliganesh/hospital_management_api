<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class PostSurgeryDetails extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        "date",
        "patient_id",
        "post_surgery_name",
    ];


    public static $postSurgeryDetailsColumns = [
        "id",
        "date",
        "post_surgery_name",
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Summary of patient
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Patient, PostSurgeryDetails>
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

}
