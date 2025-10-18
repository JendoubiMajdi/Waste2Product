<?php

namespace App\Providers;

use App\Actions\Auth\ResetsUserPasswords;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\ResetsUserPasswords as ResetsUserPasswordsContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ResetsUserPasswordsContract::class, ResetsUserPasswords::class);
        // Suppression du binding Fortify reset password (non compatible)
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
