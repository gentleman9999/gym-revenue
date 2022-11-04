<?php

namespace App\Domain\EndUsers\Actions;

use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\Users\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class AssignEndUserToRep extends BaseEndUserAction
{
    public function handle(EndUser $endUser, User $user)
    {
        EndUserAggregate::retrieve($endUser->id)->claim($user->id)->persist();

        return $endUser->refresh();
    }

    public function asController(ActionRequest $request, EndUser $endUser)
    {
        return $this->handle(
            $endUser,
            $request->user(),
        );
    }

    protected static function getModel(): EndUser
    {
        return new EndUser();
    }

    protected static function getAggregate(): EndUserAggregate
    {
        return new EndUserAggregate();
    }

    public function htmlResponse(EndUser $lead): RedirectResponse
    {
        Alert::success("Lead '{$lead->name}' assigned to Rep")->flash();

        return Redirect::back();
    }
}
