<?php
namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
        'description',
        "department_type",
        // 'type_of_department'
    ];

    // Casts for specific data types
    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $columns = [
        'id',
        'name',
        'code',
        'is_active',
        // 'description',
        "department_type",
        // 'type_of_department',
    ];
}
