<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class BillingServiceCategory extends Model
{
    protected $table = 'billing_service_category';

    protected $appends = ['name'];

    public function getNameAttribute()
    {
        return $this->category_name;
    }

    protected $fillable = [
        'category_name',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $columns = ['id', 'category_name', 'status'];
}
