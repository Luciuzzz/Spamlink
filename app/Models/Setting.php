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
        'latitude',
        'longitude',
        'user_id',

        // Branding
        'logo_path',
        'favicon_path',

        // Meta SEO
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_image_path',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
