<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class MedicineCategories extends Model
{
    protected $fillable = ['category_name'];
    protected $hidden = ['created_at', 'updated_at'];
}
