<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IPDPreOperativeAnaesthesiaEvaluation extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ipd_pre_operative_anaesthesia_evaluation';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ipd_id',
        'ipd_surgery_id',
        'ipd_anaesthesia_id',
        'previous_anaesthesia_surgery',
        'current_medication',
        'allergies',
        'asa_grading',
        'airway_assessment',
        'respiratory_system',
        'cardio_vascular_system',
        'cns_musculoskeletal',
        'hepatic_renal',
        'endocrine',
        'other_system',
        'clinical_evaluation',
        'hb_hct',
        'tc',
        'platelets',
        'bt_ct',
        'pt_ptt',
        'inr',
        'blood_group',
        'fbs_rbs',
        'bun',
        'na_k',
        'chest_xray',
        'ecg',
        'echo',
        'other_investigation',
        'specific_anaesthesia_problem',
        'pre_operative_anaesthesia_instruction',
        'summary',
        'datetime',
        'upload_pdf_path',
        'mouth_opening',
        'teeth',
        'neck_movement',
        'mallampati_score',
        'dentures_check',
        'tmd',
    ];

    public static $columns = [
        'id',
        'ipd_id',
        'ipd_surgery_id',
        'ipd_anaesthesia_id',
        'previous_anaesthesia_surgery',
        'current_medication',
        'allergies',
        'asa_grading',
        'airway_assessment',
        'respiratory_system',
        'cardio_vascular_system',
        'cns_musculoskeletal',
        'hepatic_renal',
        'endocrine',
        'other_system',
        'clinical_evaluation',
        'hb_hct',
        'tc',
        'platelets',
        'bt_ct',
        'pt_ptt',
        'inr',
        'blood_group',
        'fbs_rbs',
        'bun',
        'na_k',
        'chest_xray',
        'ecg',
        'echo',
        'other_investigation',
        'specific_anaesthesia_problem',
        'pre_operative_anaesthesia_instruction',
        'summary',
        'datetime',
        'upload_pdf_path',
        'mouth_opening',
        'teeth',
        'neck_movement',
        'mallampati_score',
        'dentures_check',
        'tmd',
        'created_at',
        'updated_at',
    ];

    public static $filter = [
        'ipd_id',
        'ipd_surgery_id',
        'ipd_anaesthesia_id',
        'datetime',
    ];

    public static $listcolumns = [
        'id',
        'ipd_id',
        'ipd_surgery_id',
        'asa_grading',
        'blood_group',
        'datetime',
        'summary',
    ];

    /**
     * Get the IPD associated with this pre-operative anaesthesia evaluation
     */
    public function ipd()
    {
        return $this->belongsTo(IPD::class, 'ipd_id', 'id');
    }

    /**
     * Get the IPD surgery associated with this pre-operative anaesthesia evaluation
     */
    public function ipdSurgery()
    {
        return $this->belongsTo(IPDSurgery::class, 'ipd_surgery_id', 'id');
    }

    /**
     * Get the IPD anesthesia associated with this pre-operative anaesthesia evaluation
     */
    public function ipdAnesthesia()
    {
        return $this->belongsTo(IPDAnesthesia::class, 'ipd_anaesthesia_id', 'id');
    }
}
