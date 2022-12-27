<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Models\EndUser;
use App\Domain\Users\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class AssignEndUserToRep extends BaseEndUserAction
{
    public function handle(EndUser $end_user, User $user): EndUser
    {
        if ($end_user->isEndUser()) {
            UserAggregate::retrieve($end_user->id)->claim($user->id)->persist();

            return $end_user->refresh();
        }

        return $end_user;
    }

    public function asController(ActionRequest $request, EndUser $end_user): EndUser
    {
        return $this->handle(
            $end_user,
            $request->user(),
        );
    }

    public function htmlResponse(EndUser $lead): RedirectResponse
    {
        if ($lead->isEndUser()) {
            Alert::success("Lead '{$lead->name}' assigned to Rep")->flash();
        } else {
            Alert::error("Cannot perform this action on this user type.");
        }

        return Redirect::back();
    }
}
