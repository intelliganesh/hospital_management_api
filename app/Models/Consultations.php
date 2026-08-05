<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Consultations extends Model
{
    use HasFactory, HasUuids;

    /**
     * Summary of scopeUpcomingFirst
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeUpcomingFirst(Builder $query): Builder
    {
        $today = Carbon::today()->toDateString();

        return $query->join('appointments', 'consultations.appointment_id', '=', 'appointments.id')
            ->where('consultations.status', 'Pending')
            ->whereDate('appointments.appointment_date', '>=', $today)
            ->orderByRaw("CASE
                WHEN appointments.appointment_date = ? THEN 0
                ELSE 1
            END", [$today])
            ->orderBy('appointments.appointment_date', 'asc');
    }

    public function scopeOnlyDoctorRelatedIfDoctorLogedIn(Builder $query): Builder
    {
        $user = Auth::user();
        if ($user && $user->hasRole('Doctor')) {
            return $query->where('consultations.doctor_id', $user->id);
        }
        return $query;
    }
    // protected static function booted()
    // {
    //     static::addGlobalScope('orderByCreatedAt', function (Builder $builder) {
    //         $builder->orderBy('created_at', 'desc');
    //     });
    // }
    protected $fillable = [
        'type', // proctology or non proctology
        'advice',
        'status',
        'complaint',
        'doctor_id',
        "medical_id",
        'patient_id',
        "surgical_cost",
        'appointment_id',
        'payment_status',
        'payment_date',
        'next_visit_date',
        "appointment_type",
        'appointment_number',
        'front_desk_user_id',
        "consultation_amount",
        "advice_admition_date",
        'test_in_same_hospital',
        'preliminary_diagnosis',
        'post_surgery_details',
        'removed',
        'external_appointment_id',

        // //medical_history
        // "chief_complaints",
        // "surgical_history",
        // "co_morbidities",
        // "on_examination",
        // "treatment_plan",
        // "tests",
        // "diet_plan",

        // //estimated cost
        // "amount",
        // "currency",

        // "additional_cost",

        'test_id',

        'fees',
        'advice_admition',

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
    protected $casts = [
        'next_visit_date' => 'date',
    ];
    protected $hidden = [
        // 'created_at',
        'updated_at',
        'appointment',
    ];

    protected $appends = ['appointment_date', 'appointment_time'];

    public $incrementing = false;
    protected $keyType   = 'string';

    public static $column = [
        'id',
        'appointment_id',
        'appointment_number',
        'status',
        "created_at",
        'patient_name',
        'patient_number',
        'payment_status',
        'next_visit_date',
        'referred_by_name',
        'type',
        'removed',
        'doctor_name',
        'patient_id',
        'type', // proctology or non proctology
        'advice',
        'status',
        'complaint',
        'doctor_id',
        "medical_id",
        'patient_id',
        "surgical_cost",
        'payment_status',
        'payment_date',
        'next_visit_date',
        "appointment_type",
        'appointment_number',
        'front_desk_user_id',
        'consultation_amount',
        'advice_admition_date',
        'test_in_same_hospital',
        'preliminary_diagnosis',
        'removed',
        'test_id',
        'fees',
        'advice_admition',
        'patient_name',
        'patient_email',
        'patient_phone',
        'doctor_name',
        'doctor_email',
        'doctor_phone',
        'front_desk_user_name',
        'front_desk_user_email',
        'front_desk_user_phone',
        'referred_by_name',
        'external_appointment_id',

    ];

    public static $filters = ["created_at", "preliminary_diagnosis", 'patient_name', 'referred_by_name', 'status', 'next_visit_date', 'payment_status', 'type', 'doctor_name'];
    //diagnosis, examination
    // public static $consultationsCreateOrUpdateColumns = ['test_id', 'appointment_id', 'patient_id', 'doctor_id', 'front_desk_user_id', 'preliminary_diagnosis', 'advice', 'next_visit_date', 'complaint', 'status'];
    public static $consultationsCreateOrUpdateColumns = [
        'advice',
        'status',
        "patient_name",
        "referred_by_name",
        'test_id',
        'complaint',
        'doctor_id',
        'patient_id',
        'appointment_id',
        'next_visit_date',
        'front_desk_user_id',
        'preliminary_diagnosis',
        'doctor_name',
        'doctor_phone',
        'doctor_email',
        'external_appointment_id',
        'consultation_amount',
        // 'chief_complaints', 'surgical_history', 'co_morbidities', 'on_examination', 'treatment_plan', 'tests', 'amount'
    ];
    public function invoiceNumber()
    {
        return $this->hasOne(Invoice::class, 'consultation_id', 'id');
    }

    public function proctology()
    {
        return $this->hasOne(Proctology::class, 'consultation_id', 'id');
    }

    public function nonProctology()
    {
        return $this->hasOne(NonProctology::class, 'consultation_id', 'id');
    }

    public function allopathy()
    {
        return $this->hasOne(Allopathy::class, 'consultation_id', 'id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointments::class, 'appointment_id', 'id');
    }

    public function getAppointmentDateAttribute()
    {
        return $this->appointment->appointment_date ?? null;
    }

    public function getAppointmentTimeAttribute()
    {
        return $this->appointment->appointment_time ?? null;
    }

    public function getExternalAppointment()
    {
        return $this->hasOne(ExternalAppointment::class, 'id', 'external_appointment_id');
    }
}
