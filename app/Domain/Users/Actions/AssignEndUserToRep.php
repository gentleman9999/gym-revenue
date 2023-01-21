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
    public function handle(array $data): EndUser
    {
        //GraphQL Sends the endUser and user data as an array from the mutation so we have to break it down here.
        $end_user = EndUser::find($data['endUser']);
        $user = User::find($data['user']);
        if ($end_user->isEndUser()) {
            UserAggregate::retrieve($end_user->id)->claim($user->id)->persist();

            return $end_user->refresh();
        }

        return $end_user;
    }

    public function asController(ActionRequest $request, array $data): EndUser
    {
        return $this->handle(
            $data,
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
