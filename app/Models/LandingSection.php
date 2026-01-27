<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Auditable; // Trait de auditoría

class LandingSection extends Model
{
    //use Auditable; // Activamos auditoría

    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'description',
        'is_active',
        'data',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'data' => 'array', // JSON a array automáticamente
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
