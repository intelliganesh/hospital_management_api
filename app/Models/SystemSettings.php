<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSettings extends Model
{
    use HasFactory;
    protected $fillable = [
        'hospital_logo',
        'hospital_name',
        'opd_prefix',
        'opd_start_number',
        'opd_status',
        'hospital_prefix',
        'code_prefix',
        'food_prefix',
        'opd_prefix',
        'ward_prefix',
        'room_prefix',
        'bed_prefix',
        'findings_prefix',
        'findings_start_number',
        'findings_status',
        'invoice_prefix',
        'invoice_start_number',
        'invoice_status',
        'ipd_prefix',
        'ipd_start_number',
        'ipd_status',
        "billing_letter_header",
        "letter_header",
        'patient_prefix',
        'patient_start_number',
        'patient_status',
        "address",
        'currency',
        'currency_symbol',
        'appointment_prefix',
        'appointment_start_number',
        'appointment_status',
        'email_notification',
        'whatsapp_notification',
        'payment_prefix',
        'payment_start_number',
        'payment_status',
        'test_prefix',
        'test_start_number',
        'test_status',
        'voucher_prefix',
        'voucher_start_number',
        'voucher_status',
        'footer_content',

        // 'user_id',
        // 'primary_color',
        // 'text_primary_color',
        // 'bg_primary_color',
        // 'text_secondary_color',
        // 'bg_secondary_color',
        // 'secondary_color',
        // 'tertiary_color',
        // 'text_tertiary_color',
        // 'bg_tertiary_color',
        // 'theme',
        'upi',
        'qr_code',
    ];

    protected $hidden = ["created_at", "updated_at"];

}
