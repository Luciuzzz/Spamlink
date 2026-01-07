<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'company_name',
        'slogan',
        'description',
        'bg_desktop_path',
        'bg_mobile_path',
        'whatsapp_number',
        'location_text',
    ];
}
