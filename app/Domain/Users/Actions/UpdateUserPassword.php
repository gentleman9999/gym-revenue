<?php

namespace App\Domain\Users\Actions;

use function __;
use App\Actions\Users\PasswordValidationRules;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    use AsAction;

    public function handle(string $id, string $password): User
    {
        Validator::make($input, [
            'current_password' => ['required', 'string'],
            'password' => $this->passwordRules(),
        ])->after(function ($validator) use ($user, $input) {
            if (! isset($input['current_password']) || ! Hash::check($input['current_password'], $user->password)) {
                $validator->errors()->add('current_password', __('The provided password does not match your current password.'));
            }
        })->validateWithBag('updatePassword');

        return UpdateUser::run($id, ['password' => $password]);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('users.update', User::class);
    }

    public function asController(ActionRequest $request, User $user): User
    {
        return $this->handle(
            $user->id,
            $request->validated(),
        );
    }

    public function htmlResponse(User $user): RedirectResponse
    {
        Alert::success("User '{$user->name}' password was successfully updated")->flash();

        return Redirect::back();
    }

    /**
     * Validate and update the user's password.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function update($user, array $input)
    {
        $this->handle($user->id, $input['password']);
    }
}
