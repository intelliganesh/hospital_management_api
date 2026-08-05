<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IPDAnaesthesiaRecoverObservation extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ipd_anaesthesia_recover_observation';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ipd_id',
        'ipd_surgery_id',
        'ipd_anaesthesia_id',
        'surgical_procedure',
        'time_patient_received',
        'post_operative_instructions',
        'monitors',
        'post_operative_complications',
        'post_operative_medications',
        'patient_score_on_admission',
        'patient_score_before_transfer',
        'vital_monitoring',
        'transfer_to',
        'time_of_transfer',
        'pulse_at_shifting',
        'sbp_at_shifting',
        'dbp_at_shifting',
        'rr_at_shifting',
        'summary',
        'upload_pdf_path',
    ];

    protected $casts = [
        'vital_monitoring' => 'array',
    ];

    public static $columns = [
        'id',
        'ipd_id',
        'ipd_surgery_id',
        'ipd_anaesthesia_id',
        'surgical_procedure',
        'time_patient_received',
        'post_operative_instructions',
        'monitors',
        'post_operative_complications',
        'post_operative_medications',
        'patient_score_on_admission',
        'patient_score_before_transfer',
        'vital_monitoring',
        'transfer_to',
        'time_of_transfer',
        'pulse_at_shifting',
        'sbp_at_shifting',
        'dbp_at_shifting',
        'rr_at_shifting',
        'summary',
        'upload_pdf_path',
        'created_at',
        'updated_at',
    ];

    public static $filter = [
        'ipd_id',
        'ipd_surgery_id',
        'ipd_anaesthesia_id',
    ];

    public static $listcolumns = [
        'id',
        'ipd_id',
        'ipd_surgery_id',
        'ipd_anaesthesia_id',
        'surgical_procedure',
        'time_patient_received',
        'monitors',
        'transfer_to',
        'time_of_transfer',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the IPD associated with this recovery observation record
     */
    public function ipd()
    {
        return $this->belongsTo(IPD::class, 'ipd_id', 'id');
    }

    /**
     * Get the IPD surgery associated with this recovery observation record
     */
    public function surgery()
    {
        return $this->belongsTo(IPDSurgery::class, 'ipd_surgery_id', 'id');
    }

    /**
     * Get the IPD anaesthesia associated with this recovery observation record
     */
    public function anaesthesia()
    {
        return $this->belongsTo(IPDAnaesthesia::class, 'ipd_anaesthesia_id', 'id');
    }
}
