<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Client\RequestException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cf-turnstile-response' => ['required', 'string'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $token = $this->input('cf-turnstile-response');
            if (!is_string($token) || $token === '') {
                return;
            }

            try {
                $validatorTurnstile = Validator::make(
                    ['cf-turnstile-response' => $token],
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

    public function messages(): array
    {
        return [
            'cf-turnstile-response.required' => 'Por favor completa el captcha para continuar.',
            'email.unique' => 'Si los datos son correctos, ya puedes iniciar sesión.',
        ];
    }
}
