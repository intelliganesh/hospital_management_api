<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{

    protected $table = "theme";
    protected $fillable = [
        'user_id',
        'primary_color',
        'text_primary_color',
        'bg_primary_color',
        'secondary_color',
        'text_secondary_color',
        'bg_secondary_color',
        'tertiary_color',
        'text_tertiary_color',
        'system_settings_id',
        'bg_tertiary_color',
        'theme',
    ];

    protected $hidden = ["created_at", "updated_at"];
}
