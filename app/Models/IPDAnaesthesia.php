<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IPDAnaesthesia extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ipd_anaesthesia';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ipd_id',
        'ipd_surgery_id',
        'diagnosis',
        'position',
        'anaesthetist_assistant',
        'type_of_anaesthesia',
        'uploaded_consent_path',
        'consent_summary',
        'upload_anaesthesia_record_path',
        'anaesthesia_record_summary',
        'datetime',
        'patient_height',
        'patient_weight',
        'patient_community',
        'patient_mother_tongue',
    ];

    public static $columns = [
        'id',
        'ipd_id',
        'ipd_surgery_id',
        'diagnosis',
        'position',
        'anaesthetist_assistant',
        'type_of_anaesthesia',
        'uploaded_consent_path',
        'consent_summary',
        'upload_anaesthesia_record_path',
        'anaesthesia_record_summary',
        'datetime',
        'patient_height',
        'patient_weight',
        'patient_community',
        'patient_mother_tongue',
        'created_at',
        'updated_at',
    ];

    public static $filter = [
        'ipd_id',
        'ipd_surgery_id',
    ];

    public static $listcolumns = [
        'id',
        'ipd_id',
        'ipd_surgery_id',
        'diagnosis',
        'position',
        'anaesthetist_assistant',
        'type_of_anaesthesia',
        'uploaded_consent_path',
        'consent_summary',
        'upload_anaesthesia_record_path',
        'anaesthesia_record_summary',
        'datetime',
        'patient_height',
        'patient_weight',
        'patient_community',
        'patient_mother_tongue',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the IPD associated with this anaesthesia record
     */
    public function ipd()
    {
        return $this->belongsTo(IPD::class, 'ipd_id', 'id');
    }

    /**
     * Get the IPD surgery associated with this anaesthesia record
     */
    public function surgery()
    {
        return $this->belongsTo(IPDSurgery::class, 'ipd_surgery_id', 'id');
    }

    /**
     * Get the pre-operative anaesthesia evaluations associated with this anaesthesia record
     */
    public function preOperativeAnaesthesiaEvaluations()
    {
        return $this->hasMany(IPDPreOperativeAnaesthesiaEvaluation::class, 'ipd_anaesthesia_id', 'id');
    }
    
}
