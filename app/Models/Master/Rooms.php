<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Ward;

class Rooms extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'name',
        'room_number',
        'room_type',
        'floor',
        'status',
        'ward_id',
        'bed_count',
        'description'
    ];
    protected $hidden = [
        "created_at",
        "updated_at",
    ];
    public static $column = ['id','name',
        'room_number',
        'room_type',
        'floor',
        'status',
        'ward_id',
        'bed_count',
        'description'];


    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }
}
