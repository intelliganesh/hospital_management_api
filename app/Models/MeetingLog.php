<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_appointment_id',
        'room_name',
        'meeting_status',
    ];

    public function externalAppointment()
    {
        return $this->belongsTo(ExternalAppointment::class);
    }
}
