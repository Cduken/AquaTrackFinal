<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Maximum number of login attempts allowed before a temporary lockout.
     */
    protected $maxAttempts = 5;

    /**
     * Show the customer login form.
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Auth/CustomerLogin', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'csrf_token' => csrf_token(),
            'loginAttempts' => $this->getLoginAttempts(),
            'remainingTime' => $this->getRemainingTime(),
        ]);
    }

    /**
     * Show the staff/admin login form (hidden URL).
     *
     * @return Response
     */
    public function staffLogin(): Response
    {
        return Inertia::render('Auth/StaffLogin', [
            'csrf_token' => csrf_token(),
            'loginAttempts' => $this->getLoginAttempts(),
            'remainingTime' => $this->getRemainingTime(),
        ]);
    }

    /**
     * Get the number of login attempts for the current IP.
     */
    protected function getLoginAttempts()
    {
        $key = $this->throttleKey(request()->ip());
        return RateLimiter::attempts($key);
    }

    /**
     * Get remaining time in seconds for login attempts (only if locked).
     */
    protected function getRemainingTime()
    {
        $key = $this->throttleKey(request()->ip());
        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
            return RateLimiter::availableIn($key);
        }
        return 0;
    }

    /**
     * Key used by the rate limiter (per IP).
     */
    protected function throttleKey($ip)
    {
        return 'login:' . $ip;
    }

    /**
     * Decide decay seconds (lockout seconds) based on the number of attempts (new count).
     * Always return a positive number (minimum 30s).
     *
     * Levels:
     * 1-4 attempts => no immediate lockout (we still set a short decay so attempts persist)
     * 5-9 attempts => 30s lockout
     * 10-14 attempts => 60s lockout
     * 15+ attempts => 300s (5min) lockout
     *
     * @param int $attemptsNew The attempt count after increment
     * @return int seconds
     */
    protected function decayForAttempts(int $attemptsNew): int
    {
        if ($attemptsNew >= 15) {
            return 300; // 5 minutes
        } elseif ($attemptsNew >= 10) {
            return 60; // 1 minute
        } elseif ($attemptsNew >= 5) {
            return 30; // 30 seconds
        }

        // For first few attempts, keep attempts stored for at least 30s so they count toward escalation
        return 30;
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws ValidationException
     */
    public function store(LoginRequest $request)
    {
        $throttleKey = $this->throttleKey($request->ip());

        // If locked out, return error with remaining_time
        if (RateLimiter::tooManyAttempts($throttleKey, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'throttle' => ["Too many login attempts. Please try again in {$seconds} seconds."],
                'remaining_time' => [$seconds],
            ]);
        }

        try {
            $request->authenticate();

            // Clear attempts on success
            RateLimiter::clear($throttleKey);

            $user = Auth::user();
            $selectedRole = strtolower($request->input('role', 'customer'));

            // Prevent login if user is disabled
            if (!$user->enabled) {
                Activity::log('login_disabled', "Disabled user attempted login", $user, [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => ['Your account has been deactivated. Please contact the administrator.'],
                ]);
            }

            // Detect which login form was used based on the request
            $isCustomerLogin = $request->filled('account_number');
            $isStaffLogin = $request->filled('email') && in_array($selectedRole, ['admin', 'staff']);

            // Prevent admin/staff from logging in through customer form
            if ($isCustomerLogin && $user->hasAnyRole(['admin', 'staff'])) {
                Activity::log('unauthorized_access', "Admin/staff attempted customer login", $user, [
                    'ip' => $request->ip(),
                    'attempted_role' => $selectedRole,
                    'login_type' => 'customer_form'
                ]);

                Auth::logout();
                throw ValidationException::withMessages([
                    'account_number' => ['Administrative accounts must use the staff login portal.'],
                ]);
            }

            // Prevent customers from logging in through staff form
            if ($isStaffLogin && $user->hasRole('customer')) {
                Activity::log('unauthorized_access', "Customer attempted staff login", $user, [
                    'ip' => $request->ip(),
                    'attempted_role' => $selectedRole,
                    'login_type' => 'staff_form'
                ]);

                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => ['Customer accounts must use the customer login portal.'],
                ]);
            }

            // Check role mismatch for staff/admin logins
            if (in_array($selectedRole, ['admin', 'staff']) && !$user->hasRole($selectedRole)) {
                $userRoles = $user->roles->pluck('name')->implode(', ');

                Activity::log('role_mismatch', "User attempted login with wrong role", $user, [
                    'attempted_role' => $selectedRole,
                    'actual_roles' => $userRoles,
                    'login_type' => 'staff_form'
                ]);

                Auth::logout();
                throw ValidationException::withMessages([
                    'role_mismatch' => [
                        'title' => 'Role Access Denied',
                        'message' => "Your account has these roles: $userRoles. Please login through the appropriate portal for your account type."
                    ]
                ]);
            }

            $request->session()->regenerate();

            // Log successful login
            Activity::log('logged_in', "User logged in", $user, [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'role' => $selectedRole,
                'login_method' => $isCustomerLogin ? 'account_number' : 'email',
                'login_type' => $isCustomerLogin ? 'customer_form' : 'staff_form'
            ]);

            // Role-based redirects
            $redirectRoute = match (true) {
                $user->hasRole('admin') => 'admin.dashboard',
                $user->hasRole('staff') => 'staff.dashboard',
                default => 'customer.dashboard',
            };

            return redirect()->intended(route($redirectRoute));
        } catch (ValidationException $e) {
            // compute new attempts count and set decay accordingly BEFORE hitting the limiter
            $attemptsBefore = RateLimiter::attempts($throttleKey);
            $attemptsAfter = $attemptsBefore + 1;
            $decay = $this->decayForAttempts($attemptsAfter);

            // increment with computed decay
            RateLimiter::hit($throttleKey, $decay);

            $attempts = $attemptsAfter;
            $remaining = max(0, $this->maxAttempts - $attempts);

            // Determine login type for logging
            $loginField = $request->input('email') ?? $request->input('account_number');
            $loginType = $request->filled('account_number') ? 'customer_form' : 'staff_form';

            Activity::log('login_failed', "Failed login attempt", null, [
                'login_field' => $loginField,
                'ip' => $request->ip(),
                'errors' => $e->errors(),
                'attempts' => $attempts,
                'remaining_attempts' => $remaining,
                'login_type' => $loginType
            ]);

            $errors = $e->errors();

            if (RateLimiter::tooManyAttempts($throttleKey, $this->maxAttempts)) {
                $seconds = RateLimiter::availableIn($throttleKey);
                $errors['throttle'] = ["Too many login attempts. Please try again in {$seconds} seconds."];
                $errors['remaining_time'] = [$seconds];
            } else {
                $errors['attempts'] = ["{$remaining} attempts remaining."];
            }

            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Redirect to appropriate dashboard based on user role.
     */
    public function redirectToDashboard()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('staff')) {
            return redirect()->route('staff.dashboard');
        } elseif ($user->hasRole('customer')) {
            return redirect()->route('customer.dashboard');
        }

        return redirect()->route('home');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            Activity::log('logged_out', "User logged out", $user);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $request->session()->flash('logout_success', true);

        return Inertia::location('/');
    }
}
