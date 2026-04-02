<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Backfill only once for older sessions/users where this field is still null.
        if ($user && Schema::hasColumn('users', 'last_login_at') && is_null($user->last_login_at)) {
            $user->forceFill(['last_login_at' => now()])->saveQuietly();
        }

        App::setLocale('en');

        return $next($request);
    }
}
