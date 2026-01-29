<?php

return [
    'sitekey' => env('TURNSTILE_SITE_KEY'),
    'secret'  => env('TURNSTILE_SECRET_KEY'),
    'enabled' => env('TURNSTILE_ENABLED', true),
    'field'   => 'cf-turnstile-response',
];
