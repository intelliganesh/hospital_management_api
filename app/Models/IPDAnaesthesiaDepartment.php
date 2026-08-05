<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IPDAnaesthesiaDepartment extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ipd_anaesthesia_department';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ipd_id',
        'ipd_surgery_id',
        'ipd_anaesthesia_id',
        'pre_anaesthesia_state',
        'ventilated_patient',
        'npo_status',
        'patient_safety',
        'pre_oxygenation',
        'induction',
        'laryngoscopy',
        'difficult_intubation',
        'endotracheal_tube',
        'endotracheal_tube_size',
        'endotracheal_tube_fixed_at',
        'endotracheal_tube_type',
        'airway',
        'airway_size',
        'mask_anaesthesia',
        'throat_pack',
        'nasogastric_tube',
        'maintenance',
        'iv_access',
        'central_blocks_spinal',
        'central_blocks_epidural',
        'central_blocks_epidural_g',
        'central_blocks_spinal_needle_g',
        'regional_blocks',
        'nerve_stimulator',
        'regional_supplements',
        'drugs_regional',
        'monitoring',
        'temperature',
        'crystalloids_ml',
        'colloids_ml',
        'blood_ml',
        'anaesthesia_technique_brief',
        'summary',
        'abp_details',
        'cvp_details',
        'upload_pdf_path',
    ];

    public static $columns = [
        'id',
        'ipd_id',
        'ipd_surgery_id',
        'ipd_anaesthesia_id',
        'pre_anaesthesia_state',
        'ventilated_patient',
        'npo_status',
        'patient_safety',
        'pre_oxygenation',
        'induction',
        'laryngoscopy',
        'difficult_intubation',
        'endotracheal_tube',
        'endotracheal_tube_size',
        'endotracheal_tube_fixed_at',
        'endotracheal_tube_type',
        'airway',
        'airway_size',
        'mask_anaesthesia',
        'throat_pack',
        'nasogastric_tube',
        'maintenance',
        'iv_access',
        'central_blocks_spinal',
        'central_blocks_epidural',
        'central_blocks_epidural_g',
        'central_blocks_spinal_needle_g',
        'regional_blocks',
        'nerve_stimulator',
        'regional_supplements',
        'drugs_regional',
        'monitoring',
        'temperature',
        'crystalloids_ml',
        'colloids_ml',
        'blood_ml',
        'anaesthesia_technique_brief',
        'summary',
        'abp_details',
        'cvp_details',
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
        'pre_anaesthesia_state',
        'patient_safety',
        'induction',
        'maintenance',
        'monitoring',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the IPD associated with this anaesthesia department record
     */
    public function ipd()
    {
        return $this->belongsTo(IPD::class, 'ipd_id', 'id');
    }

    /**
     * Get the IPD surgery associated with this anaesthesia department record
     */
    public function surgery()
    {
        return $this->belongsTo(IPDSurgery::class, 'ipd_surgery_id', 'id');
    }

    /**
     * Get the IPD anaesthesia associated with this anaesthesia department record
     */
    public function anaesthesia()
    {
        return $this->belongsTo(IPDAnaesthesia::class, 'ipd_anaesthesia_id', 'id');
    }
}
