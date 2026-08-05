<?php

namespace App\Models\Master;

use App\Enums\ComanStatusEnum;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Model;
// use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Roles extends Model
    // /SpatieRole
{
    use HasFactory;
    protected $table = 'roles_name';

    public function scopeActive($query)
    {
        return $query->where('status', ComanStatusEnum::Active->value);
    }

    protected $fillable = [
        "name",
        "status",
        "guard_name",
        "description",
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];


    public static $columns = [
        "id",
        "name",
        "status",
        "description",
    ];
}
