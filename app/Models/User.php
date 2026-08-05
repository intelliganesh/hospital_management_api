<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    protected static function booted()
    {
        static::addGlobalScope('orderByCreatedAt', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }

    public static $columns = [
        'id',
        'name',
        'email',
        'phone',
        'designation',
        'qualification',
        'department_name',
        "role",
        'image',
        'available_days',
        'slot_duration',
        'leave_date',
        "status",
    ];

    public static $filtersColumn = [
        'name',
        'role',
        'email',
        'designation',
        'qualification',
        'department_name',
        'available_days',
        'slot_duration',
        'leave_date',
        "status",
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'image',
        'DOB',
        'age',
        'system_settings_id',
        'gender',
        "pincode",
        'password',
        "id_proof_for_pan",
        'role',
        'address',
        'country',
        "letter_header_info",
        'state',
        'city',
        'department',
        'department_code',
        'marital_status',
        'department_name',
        'designation',
        'qualification',
        "status",
        'letter_footer_info',
        'available_days',
        'slot_duration',
        'leave_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
}
