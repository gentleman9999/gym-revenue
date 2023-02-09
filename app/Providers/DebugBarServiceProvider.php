<?php

namespace App\Providers;

use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\ServiceProvider;

class DebugBarServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $isPackageInstalled = $this->app->environment(['local']);
        if ($isPackageInstalled) {
            (env('APP_ENV') <> 'local' ? Debugbar::disable() : Debugbar::enable());
        }
    }
}
