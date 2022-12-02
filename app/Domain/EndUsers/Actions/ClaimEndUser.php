<?php

namespace App\Domain\EndUsers\Actions;

use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\Users\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class ClaimEndUser extends BaseEndUserAction
{
    public function handle(EndUser $endUser, User $user): EndUser
    {
        return AssignEndUserToRep::run($endUser, $user);
    }

    /**
     * GraphQL Invoke
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): EndUser
    {
        return $this->handle(EndUser::find($args['endUser']), User::find($args['user']));
    }

    public function asController(ActionRequest $request, EndUser $endUser): EndUser
    {
        return $this->handle(
            $endUser,
            $request->user(),
        );
    }

    public function htmlResponse(EndUser $endUser): RedirectResponse
    {
        Alert::success("EndUser '{$endUser->name}' claimed")->flash();

        return Redirect::back();
    }
}
