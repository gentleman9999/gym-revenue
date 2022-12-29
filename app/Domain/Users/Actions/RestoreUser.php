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

class RestoreUser
{
    use AsAction;

    public function handle(int $user): User
    {
        UserAggregate::retrieve($user)->restore()->persist();
        ReflectUserData::run($user);

        return $user;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('users.restore', User::class);
    }

    public function asController(ActionRequest $request, User $user): User
    {
        return $this->handle(
            $user,
        );
    }

    public function htmlResponse(User $endUser): RedirectResponse
    {
        Alert::success("User '{$endUser->name}' was restored")->flash();

        return Redirect::route('users');
    }
}
