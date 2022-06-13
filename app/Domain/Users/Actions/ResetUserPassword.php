<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\PasswordValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;
use Lorisleiva\Actions\Concerns\AsAction;

//TODO: this needs to be modified to use event sourcing like UpdateUser
class ResetUserPassword implements ResetsUserPasswords
{
    use AsAction;
    use PasswordValidationRules;

    public function handle(User $user, array $data): void
    {
        Validator::make($data, [
            'password' => $this->passwordRules(),
        ])->validate();

        $user->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();
    }

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function reset($user, array $input)
    {
        $this->handle($user, $input);
//        Validator::make($input, [
//            'password' => $this->passwordRules(),
//        ])->validate();
//
//        $user->forceFill([
//            'password' => Hash::make($input['password']),
//        ])->save();
    }
}
