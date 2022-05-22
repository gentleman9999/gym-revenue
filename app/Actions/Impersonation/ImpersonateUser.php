<?php

namespace App\Actions\Impersonation;

use App\Aggregates\Clients\ClientAggregate;
use App\Aggregates\Users\UserAggregate;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class ImpersonateUser
{
    use AsAction {
        __invoke as protected invokeFromLaravelActions;
    }

    public function __invoke()
    {
        // ...
    }

    public function rules(): array
    {
        return [
            'victimId' => 'bail|required|exists:users,id',
        ];
    }

    public function handle()
    {
        $results = false;
        $data = request()->all();

        $invader = auth()->user();
        $victim = User::find($data['victimId']);

        if ($invader->can('users.impersonate', User::class)) {
            $victim->current_team_id = $invader->current_team_id;
            $invader->current_team_id = $victim->current_team_id;
            $victim->save();
            $invader->save();
            auth()->user()->impersonate($victim);
            $results = true;
        }

        // tattle on this user being sneaky in its own aggy
        UserAggregate::retrieve($invader->id)->activateUserImpersonationMode($victim->id)->persist();
        // tattle on this user hopefully running support in the "victim's" aggy
        UserAggregate::retrieve($victim->id)->activatePossessionMode($invader->id)->persist();

        // rat on this user to the paying customer - the client (aggy)
        $associated_client = $victim->associated_client()->first();
        if (! is_null($associated_client)) {
            ClientAggregate::retrieve($associated_client->value)
                ->logImpersonationModeActivity($victim->id, $invader->id)->persist();
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
            \Alert::info('You are now in impersonation mode! Disable it in the name dropdown or the bottom of the screen!')->flash();

            return redirect()->route('dashboard');
        }

        \Alert::error('Error. Impersonation mode not active.')->flash();

        return redirect()->back();
    }
}
