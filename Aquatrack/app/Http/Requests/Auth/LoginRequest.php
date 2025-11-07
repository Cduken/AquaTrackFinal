<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'in:customer,admin,staff'],
            'email' => ['required_if:role,admin,staff', 'nullable', 'string', 'email'],
            'account_number' => ['required_if:role,customer', 'nullable', 'string', 'regex:/^[0-9]{3}-[0-9]{2}-[0-9]{3}[A-Za-z]?$/'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = [];

        if ($this->role === 'customer') {
            $accountNumber = $this->account_number;

            if (strpos($accountNumber, '-') === false) {
                $cleanNumber = preg_replace('/[^A-Z0-9]/i', '', $accountNumber);

                if (strlen($cleanNumber) >= 8) {
                    $accountNumber = substr($cleanNumber, 0, 3) . '-' .
                        substr($cleanNumber, 3, 2) . '-' .
                        substr($cleanNumber, 5, 3);

                    if (strlen($cleanNumber) >= 9) {
                        $accountNumber .= substr($cleanNumber, 8, 1);
                    }
                }
            }

            $accountNumber = strtoupper($accountNumber);

            $credentials = [
                'account_number' => $accountNumber,
                'password' => $this->password,
            ];
        } else {
            $credentials = [
                'email' => $this->email,
                'password' => $this->password,
            ];
        }

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                $this->role === 'customer' ? 'account_number' : 'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'throttle' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
            'remaining_time' => $seconds, // Add this line
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $identifier = $this->role === 'customer'
            ? $this->string('account_number')
            : $this->string('email');

        return Str::transliterate(Str::lower($identifier) . '|' . $this->ip());
    }
}
