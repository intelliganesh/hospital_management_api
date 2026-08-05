<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IPDInvoiceItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ipd_invoice_items';

    protected $fillable = [
        'invoice_id',
        'ipd_id',
        'amount',
        'front_desk_user_id',
        'front_desk_user_name',
        'front_desk_user_email',
        'front_desk_user_phone',
        'service_category',
        'currency',
        'description',
        'tax_percent',
        'tax_amount',
        'service_date',
    ];

    public static $columns = [
        'invoice_id',
        'ipd_id',
        'amount',
        'front_desk_user_id',
        'front_desk_user_name',
        'front_desk_user_email',
        'front_desk_user_phone',
        'service_category',
        'currency',
        'description',
        'tax_percent',
        'tax_amount',
        'service_date',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $hidden = [
        'updated_at',
    ];

    public function ipd()
    {
        return $this->belongsTo(IPD::class, 'ipd_id', 'id');
    }

    public function frontDeskUser()
    {
        return $this->belongsTo(User::class, 'front_desk_user_id', 'id');
    }
}
