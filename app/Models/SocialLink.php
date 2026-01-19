<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SocialLink extends Model
{
    protected $table = 'social_links';

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'url',
        'icon_path',   // ícono subido por el usuario
        'icon_preset', // ícono preset (Font Awesome)
        'order',
        'is_active',
    ];

    // URL final usable en frontend
    public function fullUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => empty($this->url) ? '#' : match($this->type) {
                'whatsapp' => 'https://wa.me/' . preg_replace('/\D+/', '', $this->url),
                'email'    => 'mailto:' . urlencode($this->url),
                default    => str_starts_with($this->url, 'http') ? $this->url : 'https://' . $this->url,
            }
        );
    }

    // Clase Font Awesome del icono preset
    public function iconClass(): Attribute
    {
        return Attribute::make(
            get: fn() => [
                'facebook'  => 'fa-brands fa-facebook-f',
                'instagram' => 'fa-brands fa-instagram',
                'twitter'   => 'fa-brands fa-x-twitter',
                'threads'   => 'fa-brands fa-threads',
                'youtube'   => 'fa-brands fa-youtube',
                'tiktok'    => 'fa-brands fa-tiktok',
                'email'     => 'fa-solid fa-envelope',
                'telegram'  => 'fa-brands fa-telegram',
                'whatsapp'  => 'fa-brands fa-whatsapp',
                'linkedin'  => 'fa-brands fa-linkedin',
                'github'    => 'fa-brands fa-github',
                'pinterest' => 'fa-brands fa-pinterest',
                'website'   => 'fa-solid fa-globe',
                'default'   => 'fa-solid fa-circle',
            ][$this->icon_preset ?? 'default']
        );
    }

    // Color del icono preset
    public function iconColor(): Attribute
    {
        return Attribute::make(
            get: fn() => [
                'facebook'  => '#1877F2',
                'instagram' => '#E1306C',
                'twitter'   => '#1DA1F2',
                'threads'   => '#000000',
                'youtube'   => '#FF0000',
                'tiktok'    => '#000000',
                'email'     => '#EA4335',
                'telegram'  => '#24A1DE',
                'whatsapp'  => '#25D366',
                'linkedin'  => '#0A66C2',
                'github'    => '#181717',
                'pinterest' => '#BD081C',
                'website'   => '#4B5563',
                'default'   => '#6B7280',
            ][$this->icon_preset ?? 'default']
        );
    }

    // Relación con usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
