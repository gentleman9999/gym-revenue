<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\User;
use App\Domain\Users\UserAggregate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Contracts\DeletesUsers;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use function request;

class DeleteUser implements DeletesUsers
{
    use AsAction;

    public function handle(User $user): User
    {
        $user_id = $user->id ?? $user;
        if (is_int($user)) {
            $user = User::findOrFail($user_id);
        }

        UserAggregate::retrieve($user_id)->delete()->persist();

        return $user;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('users.delete', User::class);
    }

    public function asController(ActionRequest $request, User $user): User
    {
        $me = request()->user();
//        $non_admins = [];
//
//        foreach ($team_users as $team_user) {
//            if (! $team_user->user->inSecurityGroup(SecurityGroupEnum::ADMIN)) {
//                $non_admins[] = $team_user->id;
//            }
//        }
//
//        if (count($non_admins) < 1) {
//            Alert::error("User '{$user->name}' cannot be deleted. Too few users found on team.")->flash();
//
//            return Redirect::back();
//        }

        return $this->handle(
            $user,
        );
    }

    public function htmlResponse(User $user): RedirectResponse
    {
        Alert::success("User '{$user->name}' was deleted")->flash();

        return Redirect::route('users');
    }

    /**
     * Delete the given user.
     *
     * @param mixed $user
     * @return void
     */
    public function delete($user)
    {
        $this->handle($user);
    }
}
