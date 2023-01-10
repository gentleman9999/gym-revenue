<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Models\EndUser;
use App\Domain\Users\Models\User;
use App\Enums\UserTypesEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Contracts\DeletesUsers;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteUser implements DeletesUsers
{
    use AsAction;

    public function handle(User $user): bool
    {
        UserAggregate::retrieve($user->id)->delete()->persist();
        ReflectUserData::run($user);

        return true;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        $user = $request->user;

        return $user->user_type == UserTypesEnum::EMPLOYEE ?
            $current_user->can('users.delete', User::class) && ! $current_user->isClientUser() && $user->terminated() :
            $current_user->can('endusers.delete', EndUser::class) && $user->terminated();
    }

    public function asController(ActionRequest $request, User $user): bool
    {
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
    public function delete(User $user): User
    {
        return $user;
    }
}
