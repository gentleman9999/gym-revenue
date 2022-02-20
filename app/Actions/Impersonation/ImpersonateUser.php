<?php

namespace App\Actions\Impersonation;

use Bouncer;
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

    public function rules() : array
    {
        return [
            'victimId'  => 'bail|required|exists:users,id',
        ];
    }

    public function handle()
    {
        $results = false;
        $data = request()->all();

        $invader = auth()->user();
        $victim  = User::find($data['victimId']);

        if($invader->can('users.impersonate', User::class))
        {
            auth()->user()->impersonate($victim);
            $results = true;
        }

        // @todo - tattle on this user being sneaky in its own aggy
        // @todo - tattle on this user hopefully running support in the "victim's" aggy
        // @todo - rat on this user to the paying customer - the client (aggy)

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
            \Alert::info('You are now in impersonation mode! Disable it in the name dropdown or the bottom of the screen!')->flash();
            return redirect()->route('dashboard');
        }

        \Alert::error('Error. Impersonation mode not active.')->flash();
        return redirect()->back();

    }
}
