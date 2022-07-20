<?php

namespace App\Domain\Users\Actions;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Users\Models\User;
use App\Domain\Users\UserAggregate;
use function auth;
use function config;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use function redirect;
use function response;
use function session;

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

        if (session()->has(config('laravel-impersonate.session_key'))) {
            $coward = User::withoutGlobalScopes()->findOrFail(session()->get(config('laravel-impersonate.session_key')));
            $coward_id = $coward->id;
            $liberated = auth()->user();

            $coward->current_team_id = $liberated->current_team_id;
            $liberated->current_team_id = $coward->current_team_id;
            $coward->save();
            $liberated->save();
            auth()->user()->leaveImpersonation();
            $results = true;

            // tattle on this user being sneaky in its own aggy
            UserAggregate::retrieve($coward_id)->deactivateUserImpersonationMode($liberated->id)->persist();
            // tattle on this user hopefully running support in the "victim's" aggy
            UserAggregate::retrieve($liberated->id)->deactivatePossessionMode($coward_id)->persist();

            // rat on this user to the paying customer - the client (aggy)
            if (! is_null($liberated->client->id ?? null)) {
                ClientAggregate::retrieve($liberated->client->id)
                    ->logImpersonationModeDeactivation($liberated->id, $coward_id)->persist();
            }
        }

        return $results;
    }

    public function jsonResponse($result)
    {
        $results = false;
        $code = 500;

        if ($result) {
            $results = true;
            $code = 200;
        }

        return response($results, $code);
    }

    public function htmlResponse($result)
    {
        if ($result) {
            Alert::info('Welcome back to the real world!')->flash();

            return redirect()->route('dashboard');
        }

        Alert::error('Error. Impersonation mode not active.')->flash();

        return redirect()->back();
    }
}