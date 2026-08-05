<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Ward extends Model
{
    protected $table = 'ward';

    protected $fillable = [
        'name',
        'type',
        'floor',
        'status',
        'ward_number',
        'description',
    ];


    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $columns = [
        'id',
        'name',
        'type',
        'floor',
        'status',
        'ward_number',
        'description'
    ];

}
