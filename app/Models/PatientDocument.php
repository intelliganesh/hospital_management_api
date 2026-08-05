<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PatientDocument extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'patient_documents';

    protected $fillable = [
        'patient_id',
        'document_name',
        'document_type',
        'document_path',
        'description',
        'document_date',
        'uploaded_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'document_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the patient that owns the document
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    /**
     * Get document paths as array
     */
    public function getDocumentPathsArray(): array
    {
        if (empty($this->document_path)) {
            return [];
        }
        return array_filter(explode(',', $this->document_path));
    }
}
