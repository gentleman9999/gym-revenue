<?php

declare(strict_types=1);

namespace App\Providers;

use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\ServiceProvider;

class DebugBarServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     */
    public function boot(): void
    {
        $isPackageInstalled = $this->app->environment(['local']);
        if ($isPackageInstalled) {
            (env('APP_ENV') <> 'local' ? Debugbar::disable() : Debugbar::enable());
        }
    }
}
