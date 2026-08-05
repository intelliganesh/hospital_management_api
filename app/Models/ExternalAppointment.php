<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExternalAppointment extends Model
{
    use HasFactory, HasUuids;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = 'external_appointments';

    protected $fillable = [
        'name',
        'age',
        'phone',
        'gender',
        'email',
        'citizenship',
        'place_of_living',
        'doctor_id',
        'appointment_datetime',
        'alternate_date',
        'appointment_type',
        'symptoms',
        'status',
        'amount',
        'currency',
        'meeting_link_type',
        'meeting_link',
        'payment_type',
        'payment_info',
        'visit_type',
        'transaction_id',
        'payment_date',
        'payment_screenshot',
        'appointment_reference_number',
        'daily_meeting_info',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'appointment_datetime' => 'datetime',
        'alternate_date'       => 'datetime',
        'amount'               => 'decimal:2',
        'daily_meeting_info'   => 'array',
    ];

    /**
     * Get the doctor associated with the external appointment.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the consultation associated with the external appointment.
     */
    public function consultation(): HasOne
    {
        return $this->hasOne(Consultations::class, 'external_appointment_id');
    }

    /**
     * Scope to filter appointments by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('external_appointments.status', $status);
    }

    /**
     * Scope to filter appointments by doctor
     */
    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('external_appointments.doctor_id', $doctorId);
    }

    /**
     * Scope to filter appointments by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('appointment_datetime', [$startDate, $endDate]);
    }
}
