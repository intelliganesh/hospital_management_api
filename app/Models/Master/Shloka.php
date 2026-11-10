<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Shloka extends Model
{
    protected $table = "shloka";

    protected $fillable = [
        "title",
        "verse_text",
        "status",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public static $columns = [
        'id',
        "title",
        "verse_text",
        "status",
    ];
}
