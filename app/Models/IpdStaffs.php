<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IpdStaffs extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ipd_staffs';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'ipd_id',
        'user_id',
        'user_name',
        'user_phone',
        'user_role',
        'shift',
        'assigned_date',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public static $columns = [
        'id',
        'ipd_id',
        'user_id',
        'user_name',
        'user_phone',
        'user_role',
        'shift',
        'assigned_date',
    ];

    /**
     * Get the IPD record associated with this doctor/nurse assignment.
     */
    public function ipd()
    {
        return $this->belongsTo(IPD::class, 'ipd_id', 'id');
    }

    /**
     * Get the user (doctor/nurse) associated with this assignment.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
