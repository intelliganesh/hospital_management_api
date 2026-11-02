<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class IpdDocument extends Model
{
    use HasUuids;

    public const SOURCE_GENERATED = 'generated';

    public const SOURCE_UPLOADED = 'uploaded';

    protected $table = 'ipd_documents';

    protected $fillable = [
        'id',
        'ipd_id',
        'ipd_surgery_id',
        'document_type',
        'document_path',
        'document_source',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with IPD
     */
    public function ipd()
    {
        return $this->belongsTo(IPD::class, 'ipd_id', 'id');
    }

    /**
     * Relationship with IPD Surgery
     */
    public function ipdSurgery()
    {
        return $this->belongsTo(IPDSurgery::class, 'ipd_surgery_id', 'id');
    }
}
