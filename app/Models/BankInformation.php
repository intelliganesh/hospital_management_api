<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankInformation extends Model
{
    protected $table = 'bank_information';

    protected $fillable = [
        'title',
        'details',
        'is_active',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $columns = [
        'id',
        'title',
        'details',
        'is_active',
    ];

    public static $filtersColumn = [
        'title',
        'is_active',
    ];
}
