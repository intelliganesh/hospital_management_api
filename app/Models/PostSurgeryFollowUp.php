<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class PostSurgeryFollowUp extends Model
{
    use HasFactory, HasUuids;
    protected $table = "post_surgery_followup";
    protected $fillable = [
        'date',
        'dressing',
        'appointment_number',
        'ks_changed',
        'cut_through',
        'consultation_id',
        'partial_lay_open',
        'follow_up_examination',
        'new_abscess_threading',
        'post_surgery_details_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static $filter = [
        'date',
        'dressing',
        'ks_changed',
        'cut_through',
        'appointment_number',
        'partial_lay_open',
        'follow_up_examination',
        'new_abscess_threading',
        'consultation_id',
    ];

    public static $columns = [
        'id',
        'date',
        'dressing',
        'ks_changed',
        'cut_through',
        'appointment_number',
        'consultation_id',
        'partial_lay_open',
        'follow_up_examination',
        'new_abscess_threading',
        'post_surgery_details_id',
    ];

    /**
     * Summary of postSurgeryDetails
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<PostSurgeryDetails, PostSurgeryFollowUp>
     */
    public function postSurgeryDetails()
    {
        return $this->belongsTo(PostSurgeryDetails::class);
    }


    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}
