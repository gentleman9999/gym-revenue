<?php

namespace App\Providers;

use App\Domain\Draftable\Mutations\ControllerDecoratorOverride;
use Bouncer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Lorisleiva\Actions\Decorators\ControllerDecorator;
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
        $loader = AliasLoader::getInstance();
        $loader->alias(ControllerDecorator::class, ControllerDecoratorOverride::class);
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
