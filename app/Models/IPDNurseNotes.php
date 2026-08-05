<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IPDNurseNotes extends Model
{
    use HasFactory, HasUuids;
    
    protected $table = 'ipd_nurse_notes';
    
    protected $fillable = [
        'ipd_id',
        'nurse_id',
        'nurse_name',
        'nurse_phone',
        'bp',
        'spo2',
        'temperature',
        'pulse',
        'remark1',
        'remark2',
        'datetime',
    ];

    public static $columns = [
        'id',
        'ipd_id',
        'nurse_id',
        'nurse_name',
        'nurse_phone',
        'bp',
        'spo2',
        'temperature',
        'pulse',
        'remark1',
        'remark2',
        'datetime',
    ];

    public static $filter = [
        'ipd_id',
        'nurse_id',
        'bp',
        'spo2',
        'temperature',
        'pulse',
        'datetime',
    ];

    public static $listcolumns = [
        'id',
        'ipd_id',
        'nurse_id',
        'nurse_name',
        'nurse_phone',
        'bp',
        'spo2',
        'temperature',
        'pulse',
        'remark1',
        'remark2',
        'datetime',
    ];

    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }
}
