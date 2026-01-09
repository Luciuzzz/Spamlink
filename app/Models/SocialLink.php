<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $table = 'social_links';
    protected $fillable = [
        'name',
        'url',
        'icon_path',
        'order',
        'is_active',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
