<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Master\Rooms;

class Bed extends Model
{
    use HasFactory;
    protected $table = 'bed';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'room_id',
        'bed_number',
        'status',
        'bed_type',
        'description'
    ];
    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public static $columns = [
        'id',
        'room_id',
        'bed_number',
        'status',
        'bed_type',
        'description'
    ];


    public function room()
    {
        return $this->belongsTo(Rooms::class);
    }
}
