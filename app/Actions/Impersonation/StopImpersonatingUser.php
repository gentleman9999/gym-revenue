<?php

namespace App\Actions\Impersonation;

use Lorisleiva\Actions\Concerns\AsAction;

class StopImpersonatingUser
{
    use AsAction {
        __invoke as protected invokeFromLaravelActions;
    }

    public function __invoke()
    {
        // ...
    }

    public function handle()
    {
        $results = false;

        if(session()->has(config('laravel-impersonate.session_key')))
        {
            auth()->user()->leaveImpersonation();
            $results = true;

            // @todo - tattle on this user being sneaky in its own aggy
            // @todo - tattle on this user hopefully running support in the "victim's" aggy
            // @todo - rat on this user to the paying customer - the client (aggy)
        }

        return $results;
    }

    public function jsonResponse($result)
    {
        $results = false;
        $code = 500;

        if($result)
        {
            $results = true;
            $code = 200;
        }

        return response($results, $code);
    }

    public function htmlResponse($result)
    {
        if($result)
        {
            \Alert::info('Welcome back to the real world!')->flash();
            return redirect()->route('dashboard');
        }

        \Alert::error('Error. Impersonation mode not active.')->flash();
        return redirect()->back();

    }
}
