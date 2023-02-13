<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class VaporUiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     */
    public function boot(): void
    {
        $this->gate();
    }

    /**
     * Register any application services.
     *
     */
    public function register(): void
    {
        //
    }

    /**
     * Register the Vapor UI gate.
     *
     * This gate determines who can access Vapor UI in non-local environments.
     *
     */
    protected function gate(): void
    {
        Gate::define('viewVaporUI', fn ($user = null) =>
            str_ends_with($user->email ?? '', '@capeandbay.com') 
            || str_ends_with($user->email ?? '', '@gymrevenue.com')
        );
    }
}
