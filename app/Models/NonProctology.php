<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonProctology extends Model
{
    use HasFactory, HasUuids;

    protected $table    = 'non_proctology';
    protected $fillable = [
        'yoga_asana',
        "tests",
        "diet_plan",
        "amount",
        "food_advice",
        "on_examination",
        "co_morbidities",
        "co_morbidities_description",
        "treatment_plan",
        "additional_cost",
        "chief_complaints",
        "surgical_history",
        'removed',
        // 'finding_fields',
        'medicines',
        'combination_medicines',
        'consultation_id',
        'diagnosis_summary',
        // 'food_prescription',
        // 'examination_overview',
        // 'preliminary_diagnostic',
        // 'general_advice',
        'vikruti',
        'prakriti',
        "agni",
        'koshta',
        'avastha',
        'consultation_discount',
        'currency',
        'discount_amount',
        // "benefits",
        // "asana_name",
        // "description",
        // "difficulty_level",
        // "contraindications",
        // "recommended_duration",

        "service",

        // "breakfast",
        // "lunch",
        // "dinner",
    ];
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $columns = [

    ];
}
