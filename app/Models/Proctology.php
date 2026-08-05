<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proctology extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'proctology';
    protected $fillable = [
        'doc_upload',
        'advice_field',
        'removed',
        'medicines',
        'combination_medicines',
        // 'test_id',
        "dre",
        "proctoscopy",
        "proctoscopy_anal_polyp_at",
        "dre_secondary_position",
        "proctoscopy_secondary_position",
        "dre_induration_at",
        'finding_fields',
        'consultation_id',
        'diagnosis_summary',
        'examination_overview',
        'preliminary_diagnostic',

        "service",

        //medical_history
        'preliminary_diagnosis',
        "chief_complaints",
        "surgical_history",
        "co_morbidities",
        "on_examination",
        "treatment_plan",
        "tests",
        "diet_plan",

        "additional_cost",
        //estimated cost
        "amount",
        "currency",

        "co_morbidities_description",
        "previous_scar",
        "previous_scar_position",
        "abscess",
        "abscess_position",
   
        "no_of_fistula", //single ----- Number of fistula

        "no_of_tracks_in_one_fistula", //multiple ----- Number of tracks in fistula

        "no_of_external_opening_position", //multiple ----- Number of external opening position
        "external_opening_position",//multiple ----- External Opening Position

        "internal_opening_position",//multiple ----- Internal Opening Position
        "internal_opening_distance",//multiple ----- Internal Opening Distance
        "any_other",//multiple ----- Any other

        "no_of_secondary_opening_position", //multiple ----- Number of secondary opening position
        "secondary_opening_position",//multiple ----- Internal Opening Position
        "secondary_anal_valve",//multiple ----- Secondary fistula opening position


        "other_investigation",//multiple ----- Other Investigation
        "anal_valve",//multiple ----- Anal Valve
        
        "type_of_crypt",//multiple ----- On the basis of crypt
        "crypt_cause",//multiple ----- Crypt cause

        "type_of_fistula_position",//multiple -----  On the basis of position
        "type_of_fistula_sphincter",//multiple -----  On the basis of sphincter
        "basis_of_high_low_riding",//multiple ----- On the basis of high low riding
        "distant_visceral_communication",//multiple ----- Any Other Distant or Visceral Communication 
        
        "sono_fistula_gram", //single ----- Sono Fistula Gram
        "mri_fistula_gram", //single ----- MRI Fistula Gram
        
        
        "sonologist_findings",//single
        "fistula_recurrence",//single
        "fistula_recurrence_surgery_count",//single
        "fistula_remark",//single
        "posterior_fistulous_angle",//single
        "sonologist",//single
        "managements",//single
        "managements_date",//single
        "consultation_discount",
        "discount_amount",
    ];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $columns = [
        
        'doc_upload',
        'advice_field',
        'removed',
        'medicines',
        'combination_medicines',
        // 'test_id',
        "dre",
        "proctoscopy",
        "proctoscopy_anal_polyp_at",
        "dre_secondary_position",
        "proctoscopy_secondary_position",
        "dre_induration_at",
        'finding_fields',
        'consultation_id',
        'diagnosis_summary',
        'examination_overview',
        'preliminary_diagnostic',

        "service",

        //medical_history
        'preliminary_diagnosis',
        "chief_complaints",
        "surgical_history",
        "co_morbidities",
        "on_examination",
        "treatment_plan",
        "tests",
        "diet_plan",

        "additional_cost",
        //estimated cost
        "amount",
        "currency",

        "co_morbidities_description",
        "previous_scar",
        "previous_scar_position",
        "abscess",
        "abscess_position",
   
        "no_of_fistula", //single ----- Number of fistula

        "no_of_tracks_in_one_fistula", //multiple ----- Number of tracks in fistula

        "no_of_external_opening_position", //multiple ----- Number of external opening position
        "external_opening_position",//multiple ----- External Opening Position

        "internal_opening_position",//multiple ----- Internal Opening Position
        "any_other",//multiple ----- Any other

        "no_of_secondary_opening_position", //multiple ----- Number of secondary opening position
        "secondary_opening_position",//multiple ----- Internal Opening Position
        "secondary_anal_valve",//multiple ----- Secondary fistula opening position


        "other_investigation",//multiple ----- Other Investigation
        "anal_valve",//multiple ----- Anal Valve
        
        "type_of_crypt",//multiple ----- On the basis of crypt
        "crypt_cause",//multiple ----- Crypt cause

        "type_of_fistula_position",//multiple -----  On the basis of position
        "type_of_fistula_sphincter",//multiple -----  On the basis of sphincter
        "basis_of_high_low_riding",//multiple ----- On the basis of high low riding
        "distant_visceral_communication",//multiple ----- Any Other Distant or Visceral Communication 
        
        "sono_fistula_gram", //single ----- Sono Fistula Gram
        "mri_fistula_gram", //single ----- MRI Fistula Gram
        
        
        "sonologist_findings",//single
        "fistula_recurrence",//single
        "fistula_recurrence_surgery_count",//single
        "fistula_remark",//single
        "posterior_fistulous_angle",//single
        "sonologist",//single
        "managements",//single
        "managements_date",//single
        "consultation_discount",
        "discount_amount",
    

        // 'preliminary_diagnosis',
        // "chief_complaints",
        // "surgical_history",
        // "co_morbidities",
        // "on_examination",
        // "treatment_plan",
        // "tests",
        // "diet_plan",
    ];

}
