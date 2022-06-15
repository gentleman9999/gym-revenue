<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Lab404\Impersonate\Events\LeaveImpersonation;
use Lab404\Impersonate\Events\TakeImpersonation;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //----Fix for Laravel 9 + Impersonate + Sanctum causing instant logout
        //https://github.com/404labfr/laravel-impersonate/issues/134
        //TODO:whenever laravel-impersonate is updated, try removing this block to
        //TODO:see if this fix is still needed.
        Event::listen(function (TakeImpersonation $event) {
            session()->put([
                'password_hash_sanctum' => $event->impersonated->getAuthPassword(),
            ]);
        });

        Event::listen(function (LeaveImpersonation $event) {
            session()->put([
                'password_hash_sanctum' => $event->impersonator->getAuthPassword(),
            ]);
        });
        //----END Fix for Laravel 9 + Impersonate + Sanctum
    }
}
