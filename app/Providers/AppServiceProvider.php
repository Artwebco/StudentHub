<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            $user = $event->user;

            if (!$user || !Schema::hasColumn('users', 'last_login_at')) {
                return;
            }

            User::whereKey($user->getAuthIdentifier())->update(['last_login_at' => now()]);
        });
    }
}
