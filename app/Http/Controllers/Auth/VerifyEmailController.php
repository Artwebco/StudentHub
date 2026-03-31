<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            if ($user->role === 'admin') {
                return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
            } else {
                return redirect()->intended(route('student.my-statistic', absolute: false).'?verified=1');
            }
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        if ($user->role === 'admin') {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        } else {
            return redirect()->intended(route('student.my-statistic', absolute: false).'?verified=1');
        }
    }
}
