<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;
use Illuminate\Http\Client\RequestException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];

        if (config('services.turnstile.enabled', false)) {
            $rules['cf-turnstile-response'] = ['required'];
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        if (! config('services.turnstile.enabled', false)) {
            return;
        }

        $validator->after(function ($validator) {
            try {
                $validatorTurnstile = Validator::make(
                    ['cf-turnstile-response' => $this->input('cf-turnstile-response')],
                    ['cf-turnstile-response' => [new Turnstile()]]
                );

                if ($validatorTurnstile->fails()) {
                    $validator->errors()->add(
                        'cf-turnstile-response',
                        'Captcha no válido. Intenta de nuevo.'
                    );
                }
            } catch (RequestException $e) {
                $validator->errors()->add(
                    'cf-turnstile-response',
                    'Error de configuración del captcha.'
                );
            }
        });
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) return;

        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')) . '|' . $this->ip());
    }
}
