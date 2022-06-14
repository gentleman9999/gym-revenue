<?php

namespace App\Domain\Users\Actions;

use function __;
use App\Domain\Users\Models\User;
use App\Domain\Users\PasswordValidationRules;
use App\Domain\Users\UserAggregate;
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

    public function handle(User $user, array $data): void
    {
        Validator::make($data, [
            'current_password' => ['required', 'string'],
            'password' => $this->passwordRules(),
        ])->after(function ($validator) use ($user, $data) {
            if (! isset($data['current_password']) || ! Hash::check($data['current_password'], $user->password)) {
                $validator->errors()->add('current_password', __('The provided password does not match your current password.'));
            }
        })->validateWithBag('updatePassword');
        UserAggregate::retrieve($user->id)->updatePassword(bcrypt($data['password']))->persist();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('users.update', User::class);
    }

    public function asController(ActionRequest $request, User $user): void
    {
        $this->handle(
            $user->id,
            $request->validated(),
        );
    }

    public function htmlResponse(User $user): RedirectResponse
    {
        Alert::success("Your password was successfully updated")->flash();

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
        $this->handle($user, $input);
    }
}
