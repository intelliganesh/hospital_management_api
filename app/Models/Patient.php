<?php
namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    // class Patient extends MaskId
    use HasFactory, HasUuids;

    // Disable Laravel's automatic timestamp handling
    public $timestamps = false;

    protected $table = 'patients';

    protected static function booted()
    {
        static::addGlobalScope('orderByCreatedAt', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }

    protected $fillable = [
        "dob",
        "age",
        "city",
        "place_of_living",
        "email",
        "state",
        "status",
        "gender",
        "country",
        "address",
        "pincode",
        "phone_no",
        "last_name",
        "first_name",
        "opd_number",
        "amount_for",
        "id_proof_for_pan",
        // "enroll_fees",
        "blood_group",
        // "referred_to",
        // "referred_by",
        // "payment_type",
        // "surgery_status",
        "patient_number",
        // "payment_status",
        "marital_status",
        // "referral_status",
        "referred_by_name",
        // "admission_status",
        // "emergency_status",
        // "treatment_status",
        // "referred_by_email",
        "front_desk_user_id",
        "dietary_preference",
        "insurance_provider",
        "insurance_policy_no",
                                           // "referred_by_phone_no",
                                           // "referred_by_hospital_name",
                                           // "referred_by", // relative or attened person with patient
                                           // "referred_by_phone_no", // relative or attened person with patient
        "attendant_with_patient_name",     // relative or attened person with patient
        "attendant_with_patient_phone_no", // relative or attened person with patient
        "created_at",
        "updated_at",
    ];

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $hidden = [
        // 'created_at',
        // 'updated_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['name', 'front_desk_user_name'];

    public static $columns = ['id', "email", 'patient_number', 'dietary_preference', 'first_name', 'last_name', 'age', 'phone_no', 'status', 'opd_number', 'gender', 'front_desk_user_id'];

    public static $filter = ['first_name', 'last_name', 'created_at', 'gender', 'country', 'age'];

    public function appointments()
    {
        return $this->hasMany(Appointments::class)->orderBy('created_at', 'desc');
    }

    public function consultation()
    {
        return $this->hasMany(Consultations::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get all documents for this patient
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents()
    {
        return $this->hasMany(PatientDocument::class, 'patient_id', 'id')->orderBy('created_at', 'desc');
    }

    /**
     * Get the front desk user who registered/manages this patient
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function frontDeskUser()
    {
        return $this->belongsTo(User::class, 'front_desk_user_id');
    }

    /**
     * Get the patient's full name by concatenating first and last name
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the front desk user's name who registered/manages this patient
     *
     * @return string|null
     */
    public function getFrontDeskUserNameAttribute()
    {
        return $this->frontDeskUser ? $this->frontDeskUser->name : null;
    }

    public function getDocuments()
    {
        $documents = \App\Models\PatientDocument::where('patient_id', $this->id)->orderBy('created_at', 'DESC')->get();
        $docs      = [];
        if (! is_null($documents)) {
            foreach ($documents as $document) {
                $docs[] = $document->document_path;
            }
            return implode(",", $docs);
        } else {
            return '';
        }
    }

    public function ipdCreatedOrNot()
    {
        return $this->hasMany(IPD::class, 'patient_id', 'id');
    }
}
