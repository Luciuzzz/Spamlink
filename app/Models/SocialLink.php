<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SocialLink extends Model
{
    protected $table = 'social_links';

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'url',
        'icon_path',
        'order',
        'is_active',
    ];

    /**
     * Accesor para obtener el enlace real funcional.
     * Uso: $socialLink->full_url
     */
    protected function fullUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $value = $this->url;
                if (empty($value)) {
                    return '#';
                }

                switch ($this->type) {
                    case 'whatsapp':
                        // Solo números
                        $phone = preg_replace('/\D+/', '', $value);
                        return "https://wa.me/{$phone}";

                    // Cliente de correo local
                    /* case 'email':
                        $encodedEmail = urlencode($value);
                        return "mailto:{$encodedEmail}"; */

                    // Gmail web
                    case 'email':
                        // Codificamos el correo por si tiene caracteres especiales
                        $encodedEmail = urlencode($value);
                        return "https://mail.google.com/mail/?view=cm&fs=1&to={$encodedEmail}";

                    default:
                        // Enlaces web normales
                        return str_starts_with($value, 'http') ? $value : "https://{$value}";
                }
            }
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
