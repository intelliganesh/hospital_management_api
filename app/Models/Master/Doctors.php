<?php
namespace App\Models\Master;

use App\Enums\DoctorStatusEnum;
use Illuminate\Database\Eloquent\Model;

class Doctors extends Model
{
    protected $fillable = [
        'dob',
        'email',
        'photo',
        'gender',
        'status',
        'address',
        'full_name',
        'doctor_code',
        'phone_number',
        'qualification',
        'department_id',
        'specialization',
        'registration_no',
        'consulting_type',
        'experience_years',
        'availability_days',
        'available_timings',
    ];

    // Cast fields
    protected $casts = [
        'dob'               => 'date',
        'availability_days' => 'array',
        'status'            => DoctorStatusEnum::class,
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $columns = [
        'id',
        'email',
        'gender',
        'full_name',
        'phone_number',
        'qualification',
        'experience_years',
    ];

}
