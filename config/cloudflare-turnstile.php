<?php

return [
    'sitekey' => env('TURNSTILE_SITE_KEY'),
    'secret'  => env('TURNSTILE_SECRET_KEY'),
    'enabled' => env('TURNSTILE_ENABLED', true),
    'field'   => env('TURNSTILE_FIELD', 'cf-turnstile-response'),
    'url'     => env('TURNSTILE_URL', 'https://challenges.cloudflare.com/turnstile/v0/siteverify'),
];
