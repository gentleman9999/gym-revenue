<?php

namespace App\Providers;

use App\Models\Role;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Bouncer;
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
        Actions::registerCommands();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Bouncer::useRoleModel(Role::class);
    }
}
