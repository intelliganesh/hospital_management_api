<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IPDSurgery extends Model
{
    use HasFactory, HasUuids;
    
    protected $table = 'ipd_surgery';
    
    protected $fillable = [
        'ipd_id',
        'surgery_name',
        'surgery_type',
        'surgery_date',
        'status',
        'surgeon',
        'anaesthetist',
        'external_anaesthetist',
        'department',
        'surgery_start_datetime',
        'surgery_end_datetime',
        'assistant_surgeon',
        'scrub_nurse',
        'specimen_for_hpe',
        'operative_notes',
        'operative_findings',
        'post_operative_instructions',
        'summary',
        'uploaded_report_path',
        'uploaded_consent_path',
        'consent_summary',
    ];

    public static $columns = [
        'id',
        'ipd_id',
        'surgery_name',
        'surgery_type',
        'surgery_date',
        'status',
        'surgeon',
        'anaesthetist',
        'external_anaesthetist',
        'department',
        'surgery_start_datetime',
        'surgery_end_datetime',
        'assistant_surgeon',
        'scrub_nurse',
        'specimen_for_hpe',
        'operative_notes',
        'operative_findings',
        'post_operative_instructions',
        'summary',
        'uploaded_report_path',
        'uploaded_consent_path',
        'consent_summary',
    ];

    public static $filter = [
        'ipd_id',
        'surgery_type',
        'status',
        'surgery_date',
    ];

    public static $listcolumns = [
        'id',
        'ipd_id',
        'surgery_name',
        'surgery_type',
        'surgery_date',
        'status',
        'surgeon',
        'anaesthetist',
        'external_anaesthetist',
        'department',
        'surgery_start_datetime',
        'surgery_end_datetime',
        'assistant_surgeon',
        'scrub_nurse',
        'specimen_for_hpe',
        'operative_notes',
        'operative_findings',
        'post_operative_instructions',
        'summary',
        'uploaded_report_path',
        'uploaded_consent_path',
        'consent_summary',
    ];
}
