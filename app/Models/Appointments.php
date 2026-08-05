<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Appointments extends Model
{
    use HasFactory, HasUuids;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Summary of scopeTodayFirst
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopeTodayFirst(Builder $query): Builder
    {
        $today = Carbon::today();

        return $query->whereDate('appointment_date', '>=', $today)
            ->orderByRaw("CASE
                WHEN appointment_date = ? THEN 0
                ELSE 1
            END", [$today])
            ->orderBy('appointment_date', 'asc');
    }

    /**
     * Summary of scopeOnlyDoctorRelatedIfDoctorLogedIn
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopeOnlyDoctorRelatedIfDoctorLogedIn(Builder $query): Builder
    {
        $user = Auth::user();
        if ($user && $user->hasRole('Doctor')) {
            return $query->where('doctor_id', $user->id);
        }
        return $query;
    }

    protected $fillable = [
        'consultation_type',
        'type',
        'status',
        'doctor_id',
        'front_desk_user_id',
        'complaint',
        'patient_id',
        // 'enroll_fees',
        // 'appointment_fees',
        'appointment_time',
        'appointment_date',
        'appointment_number',
        'payment_status',

        // Snapshot fields for patient
        'patient_name',
        'patient_email',
        'patient_phone',
        "patient_number",

        // Snapshot fields for doctor
        'doctor_name',
        'doctor_email',
        'doctor_phone',

        // Snapshot fields for front desk user
        'front_desk_user_name',
        'front_desk_user_email',
        'front_desk_user_phone',

        'referred_by_name',
        // 'referred_by_email',
        // 'referred_by_phone_no',
        // 'referred_by_hospital_name',
    ];
    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public static $filter = [
        'type',
        'status',
        'doctor_id',
        'front_desk_user_id',
    ];

    public static $columns = ['consultation_type','id', 'appointment_number', 'patient_name', 'patient_phone', 'type', 'appointment_date', 'appointment_time', 'status', 'doctor_name'];

    public static $updateOrCreateColumns = ['consultation_type','type', 'status', 'doctor_id', 'appointment_date', 'complaint', 'patient_id', 'appointment_fees', 'appointment_time', 'front_desk_user_id', 'referred_by_name', 'doctor_name', 'doctor_phone', 'doctor_email'];

    public static $appointmentValidationColumns = ['consultation_type','patient_id', 'doctor_id', 'complaint', 'type', 'appointment_fees', 'appointment_date', 'appointment_time', 'status', 'front_desk_user_id'];

    public function consultationOnlyDepartmentType()
    {
        return $this->hasOne(Consultations::class, 'appointment_id', 'id')->selectRaw('appointment_id, type AS department_type');
    }

}
