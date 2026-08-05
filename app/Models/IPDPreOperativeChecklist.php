<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IPDPreOperativeChecklist extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ipd_pre_operative_checklist';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ipd_id',
        'ipd_surgery_id',
        'q01_investigations',
        'q02_chest_xray_ecg',
        'q03_minor_age_parents',
        'q04a_blood_thinners',
        'q04b_blood_thinners_details',
        'q05a_asthma',
        'q05b_asthma_treatment',
        'q06_medication_allergy',
        'q07_tooth_extraction',
        'q08_surgical_procedure',
        'q09a_diabetic',
        'q09b_blood_sugar',
        'q10_thyroid_medication',
        'q11a_hypertension',
        'q11b_hypertension_medicine',
        'q11c_hypertension_medication_taken',
        'q12_informed_consent',
        'q13_anesthesia_awareness',
        'q14_operative_procedure_awareness',
        'q15a_male_patient_age',
        'q15b_urinary_symptoms',
        'q16_urinary_obstruction',
        'q17_lithotomy_position',
        'q18_previous_surgery',
        'q19_community',
        'q20_previous_surgery_events',
        'q21_female_pregnant',
        'q22_epilepsy',
        'q23_antipsychotic',
        'q24_last_food_intake',
        'summary',
        'datetime',
        'upload_pdf_path',
    ];

    public static $columns = [
        'id',
        'ipd_id',
        'q01_investigations',
        'q02_chest_xray_ecg',
        'q03_minor_age_parents',
        'q04a_blood_thinners',
        'q04b_blood_thinners_details',
        'q05a_asthma',
        'q05b_asthma_treatment',
        'q06_medication_allergy',
        'q07_tooth_extraction',
        'q08_surgical_procedure',
        'q09a_diabetic',
        'q09b_blood_sugar',
        'q10_thyroid_medication',
        'q11a_hypertension',
        'q11b_hypertension_medicine',
        'q11c_hypertension_medication_taken',
        'q12_informed_consent',
        'q13_anesthesia_awareness',
        'q14_operative_procedure_awareness',
        'q15a_male_patient_age',
        'q15b_urinary_symptoms',
        'q16_urinary_obstruction',
        'q17_lithotomy_position',
        'q18_previous_surgery',
        'q19_community',
        'q20_previous_surgery_events',
        'q21_female_pregnant',
        'q22_epilepsy',
        'q23_antipsychotic',
        'q24_last_food_intake',
        'summary',
        'datetime',
        'upload_pdf_path',
    ];

    public static $filter = [
        'ipd_id',
        'datetime',
    ];

    public static $listcolumns = [
        'id',
        'ipd_id',
        'q01_investigations',
        'q02_chest_xray_ecg',
        'q03_minor_age_parents',
        'q04a_blood_thinners',
        'q04b_blood_thinners_details',
        'q05a_asthma',
        'q05b_asthma_treatment',
        'q06_medication_allergy',
        'q07_tooth_extraction',
        'q08_surgical_procedure',
        'q09a_diabetic',
        'q09b_blood_sugar',
        'q10_thyroid_medication',
        'q11a_hypertension',
        'q11b_hypertension_medicine',
        'q11c_hypertension_medication_taken',
        'q12_informed_consent',
        'q13_anesthesia_awareness',
        'q14_operative_procedure_awareness',
        'q15a_male_patient_age',
        'q15b_urinary_symptoms',
        'q16_urinary_obstruction',
        'q17_lithotomy_position',
        'q18_previous_surgery',
        'q19_community',
        'q20_previous_surgery_events',
        'q21_female_pregnant',
        'q22_epilepsy',
        'q23_antipsychotic',
        'q24_last_food_intake',
        'summary',
        'datetime',
        'upload_pdf_path',
    ];


    public function surgery(){
        return $this->belongsTo(IPDSurgery::class, 'ipd_surgery_id');
    }
}
