<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class ReinstateUser
{
    use AsAction;

    public function handle(User $user): User
    {
        UserAggregate::retrieve($user->id)->reinstate()->persist();

        return $user;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        $user = $request->user;

        return $user->user_type == UserTypesEnum::EMPLOYEE ?
            $current_user->can('users.restore', User::class) && $user->terminated() :
            $current_user->can('endusers.restore', EndUser::class) && $user->terminated();
    }

    public function asController(ActionRequest $request, User $user): User
    {
        return $this->handle(
            $user,
        );
    }

    public function htmlResponse(User $endUser): RedirectResponse
    {
        Alert::success("User '{$endUser->name}' was reinstated")->flash();

        return Redirect::route('users');
    }
}
