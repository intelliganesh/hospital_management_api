<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Allergies extends Model
{
    use HasFactory;
    
    /**
     * The "booted" method of the model.
     * Automatically sets documented_by field with current authenticated user's name
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($allergy) {
            if (empty($allergy->documented_by) && auth()->check()) {
                $allergy->documented_by = auth()->user()->name;
            }
        });
        
        // static::updating(function ($allergy) {
        //     if (empty($allergy->documented_by) && auth()->check()) {
        //         $allergy->documented_by = auth()->user()->name;
        //     }
        // });
    }

    protected $fillable = [
        'allergen_name',
        'allergen_type',
        // 'reaction_type',
        // 'severity',
        'other_allergen_type',
        // 'date_first_experienced',
        // 'management',
        'documented_by',
        "department_type",
        'notes',
    ];

    public static $updateOrCreateColumns = ['allergen_name', 'allergen_type', 'reaction_type', 'severity', 'date_first_experienced', 'management', 'documented_by', 'notes', 'department_type'];

}
