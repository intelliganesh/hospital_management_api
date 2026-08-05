<?php

namespace App\Models;

use App\Models\Master\Medicines;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\MedicineCategories;

class MedicineCategoryMapping extends Model
{
    protected $table = 'medicine_category_mapping';
    protected $fillable = ['medicine_id', 'category_id'];

    protected $hidden = ['created_at', 'updated_at'];

    public function medicine()
    {
        return $this->belongsTo(Medicines::class, 'medicine_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(MedicineCategories::class, 'category_id', 'id');
    }
}
