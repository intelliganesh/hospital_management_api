<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $table = "countries_data";
    protected $fillable = ['name', 'code', 'phonecode'];
}
