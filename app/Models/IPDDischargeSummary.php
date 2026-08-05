<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IPDDischargeSummary extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ipd_discharge_summary';

    protected $fillable = [
        'ipd_id',
        'doctor_incharge',
        'consultants',
        'diagnosis',
        'case_history_and_complaints',
        'general_examination',
        'systemic_examination',
        'investigations',
        'operation_done',
        'findings_and_procedure',
        'course_in_hospital',
        'patient_health_condition_at_discharge',
        'advice_on_discharge',
        'special_instruction',
        'upload_pdf_path',
    ];

    public static $columns = [
        'id',
        'ipd_id',
        'doctor_incharge',
        'consultants',
        'diagnosis',
        'case_history_and_complaints',
        'general_examination',
        'systemic_examination',
        'investigations',
        'operation_done',
        'findings_and_procedure',
        'course_in_hospital',
        'patient_health_condition_at_discharge',
        'advice_on_discharge',
        'special_instruction',
        'upload_pdf_path',
    ];

    public static $filter = [
        'ipd_id',
    ];

    public static $listcolumns = [
        'id',
        'ipd_id',
        'doctor_incharge',
        'consultants',
        'diagnosis',
        'case_history_and_complaints',
        'general_examination',
        'systemic_examination',
        'investigations',
        'operation_done',
        'findings_and_procedure',
        'course_in_hospital',
        'patient_health_condition_at_discharge',
        'advice_on_discharge',
        'special_instruction',
        'upload_pdf_path',
    ];

    public function ipd()
    {
        return $this->belongsTo(IPD::class, 'ipd_id', 'id');
    }
}
