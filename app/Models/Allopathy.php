<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Allopathy extends Model
{
    use HasFactory, HasUuids;

    protected $table = "allopathy";
    protected $fillable = [
        'doc_upload',
        'advice_field',
        'medicines',
        'removed',
        // 'test_id',
        'dre',
        "proctoscopy",
        'finding_fields',
        "dre_induration_at",
        "proctoscopy_anal_polyp_at",
        "dre_secondary_position",
        "proctoscopy_secondary_position",
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

        "previous_scar",
        "previous_scar_position",
        "abscess",
        "abscess_position",
        "dre_induration_at",
        "managements",
        "managements_date",
        "combination_medicines",
        "fistula_remark",

        "no_of_fistula", //single ----- Number of fistula

        "no_of_tracks_in_one_fistula", //multiple ----- Number of tracks in fistula

        "no_of_external_opening_position", //multiple ----- Number of external opening position
        "external_opening_position",//multiple ----- External Opening Position
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
        'finding_fields',
        'consultation_id',
        'diagnosis_summary',
        'examination_overview',
        'preliminary_diagnostic',
        "previous_scar",
        "previous_scar_position",
        "abscess",
        "abscess_position",
        "managements",
        "combination_medicines",
        "fistula_remark",
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
