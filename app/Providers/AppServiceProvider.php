<?php

namespace App\Providers;

use Bouncer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Lorisleiva\Actions\Facades\Actions;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //registers any App/Actions/* that have well-defined command signatures
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::preventLazyLoading(! $this->app->isProduction());
        //Bouncer::useRoleModel(Role::class);
        if ($this->app->runningInConsole()) {
            Actions::registerCommands();
            Actions::registerCommands([
                'app/Domain/',
            ]);
        }
    }
}
