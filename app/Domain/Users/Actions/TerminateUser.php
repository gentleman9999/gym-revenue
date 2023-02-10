<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Models\EndUser;
use App\Domain\Users\Models\User;
use App\Enums\UserTypesEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TerminateUser
{
    use AsAction;

    public function handle(User $user): User
    {
        UserAggregate::retrieve($user->id)->terminate()->persist();

        return $user;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        $user         = $request->user;

        return $user->user_type == UserTypesEnum::EMPLOYEE ?
            $current_user->can('users.trash', User::class) && ! $user->terminated() :
            $current_user->can('endusers.trash', EndUser::class) && ! $user->terminated();
    }

    public function asController(ActionRequest $request, User $user): User
    {
        return $this->handle(
            $user,
        );
    }

    public function htmlResponse(User $user): RedirectResponse
    {
        Alert::success("User '{$user->name}' was terminated")->flash();

        return Redirect::route('users');
    }
}
