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
        //GraphQL Sends the endUser and user data as an array from the mutation, so we have to break it down here.
        $end_user = (new EndUser())->find($data['end_user_id']);
        if ($end_user->isEndUser()) {
            UserAggregate::retrieve($end_user->id)->claim($data['user_id'])->persist();

            return $end_user->refresh();
        }

        return $end_user;
    }

    public function asController(ActionRequest $request, string $end_user): EndUser
    {
        return $this->handle(
            ['end_user_id' => $end_user,
                'user_id' => $request->user()->id,
                ],
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
