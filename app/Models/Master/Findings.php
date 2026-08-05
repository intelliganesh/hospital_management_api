<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Findings extends Model
{

    protected $appends = ['name'];

    public function getNameAttribute()
    {
        return $this->finding_name;
    }

    protected $fillable = [
        // "finding_code",
        "finding_name",
        "findings_number",
        "finding_description",
        "category",
        "status",
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $columns = ['id', 'findings_number', 'finding_name', 'category', 'status'];
}
