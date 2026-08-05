<?php
namespace App\Models;

use App\Models\Consultations;
use App\Models\IPDDischargeSummary;
use App\Models\IPDDoctorNotes;
use App\Models\IPDInvoiceItem;
use App\Models\IPDNurseNotes;
use App\Models\IPDPreliminaryNotes;
use App\Models\IPDPreOperativeChecklist;
use App\Models\IpdStaffs;
use App\Models\IPDSurgery;
use App\Models\Master\Rooms;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IPD extends Model
{
    use HasFactory, HasUuids;

    protected $table    = 'ipd';
    const CREATED_AT    = 'created_at';
    const UPDATED_AT    = 'updated_at';
    protected $fillable = [
        'patient_id',
        'patient_name',
        'patient_number',
        'patient_email',
        'patient_phone',
        'patient_age',
        'patient_attendant_name',
        'patient_attendant_phone',
        'patient_address',
        'doctor_id',
        'doctor_name',
        'doctor_email',
        'doctor_phone',
        'consultation_id',
        'admission_date_time',
        'discharge_date_time',
        'ward_id',
        'ward_number',
        'ward_type',
        'room_id',
        'room_type',
        'room_number',
        'bed_id',
        'bed_number',
        'ipd_number',
        'status',
    ];
    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public $incrementing = false;
    protected $keyType   = 'string';

    public static $columns = [
        'id',
        'patient_number',
        'patient_id',
        'patient_name',
        'patient_email',
        'patient_phone',
        'patient_age',
        'patient_attendant_name',
        'patient_attendant_phone',
        'patient_address',
        'doctor_id',
        'doctor_name',
        'doctor_email',
        'doctor_phone',
        'consultation_id',
        'admission_date_time',
        'discharge_date_time',
        'ward_id',
        'ward_number',
        'ward_type',
        'room_id',
        'room_type',
        'room_number',
        'bed_id',
        'bed_number',
        'ipd_number',
        'status',
    ];

    /**
     * Get the patient associated with this IPD record.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    /**
     * Get the ward associated with this IPD record.
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'id');
    }

    /**
     * Get the room associated with this IPD record.
     */
    public function room()
    {
        return $this->belongsTo(Rooms::class, 'room_id', 'id');
    }

    /**
     * Get the bed associated with this IPD record.
     */
    public function bed()
    {
        return $this->belongsTo(Bed::class, 'bed_id', 'id');
    }

    /**
     * Get the consultation associated with this IPD record.
     */
    public function consultation()
    {
        return $this->belongsTo(Consultations::class, 'consultation_id', 'id');
    }

    /**
     * Get all staff assignments (doctors and nurses) for this IPD record.
     */
    public function staffs()
    {
        return $this->hasMany(IpdStaffs::class, 'ipd_id', 'id');
    }

    public function invoiceItems()
    {
        return $this->hasMany(IPDInvoiceItem::class, 'ipd_id', 'id');
    }

    /**
     * Get all consultant doctors assigned to this IPD record.
     */
    public function consultantDoctors()
    {
        return $this->hasMany(IpdStaffs::class, 'ipd_id', 'id')
            ->where('user_role', 'consultant_doctor');
    }

    /**
     * Get all duty doctors assigned to this IPD record.
     */
    public function dutyDoctors()
    {
        return $this->hasMany(IpdStaffs::class, 'ipd_id', 'id')
            ->where('user_role', 'duty_doctor');
    }

    /**
     * Get all nurses assigned to this IPD record.
     */
    public function nurses()
    {
        return $this->hasMany(IpdStaffs::class, 'ipd_id', 'id')
            ->where('user_role', 'nurse');
    }

    public function preliminaryNotes()
    {
        return $this->hasMany(IPDPreliminaryNotes::class, 'ipd_id', 'id');
    }

    public function nurseNotes()
    {
        return $this->hasMany(IPDNurseNotes::class, 'ipd_id', 'id');
    }

    public function doctorNotes()
    {
        return $this->hasMany(IPDDoctorNotes::class, 'ipd_id', 'id');
    }

    public function dischargeSummary()
    {
        return $this->hasOne(IPDDischargeSummary::class, 'ipd_id', 'id');
    }

    public function surgery()
    {
        return $this->hasMany(IPDSurgery::class, 'ipd_id', 'id');
    }

    public function preOperativeChecklist()
    {
        return $this->hasOne(IPDPreOperativeChecklist::class, 'ipd_id', 'id');
    }

    public function preOperativeAnaesthesiaEvaluation()
    {
        return $this->hasOne(IPDPreOperativeAnaesthesiaEvaluation::class, 'ipd_id', 'id');
    }
    public function anaesthesia()
    {
        return $this->hasOne(IPDAnaesthesia::class, 'ipd_id', 'id');
    }
    public function recoveryObservation()
    {
        return $this->hasOne(IPDAnaesthesiaRecoverObservation::class, 'ipd_id', 'id');
    }

    public function anaesthesiaDepartment()
    {
        return $this->hasOne(IPDAnaesthesiaDepartment::class, 'ipd_id', 'id');
    }

    public function dischargeSummaryReport()
    {
        return $this->hasOne(IPDDischargeSummary::class, 'ipd_id', 'id');
    }
}
