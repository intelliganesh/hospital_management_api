<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    protected $fillable = [
        "date",
        "image",//"proof",
        "other",
        "amount",
        "for_name",
        "entered_name",
        "description",
        "expense_name",
        "transaction_id",
        "mode_of_payment",
        "voucher_number",
    ];

    // protected $hidden = [
    //     "created_at",
    //     "updated_at",
    // ];


    public static $filter = [
        'id',
        "date",
        // "proof",
        "amount",
        "expense_name",
        "mode_of_payment",
        "voucher_number",
    ];

    public static $columns = [
        'id',
        "date",
        // "proof",
        "amount",
        "expense_name",
        "mode_of_payment",
        "voucher_number",
    ];
}
