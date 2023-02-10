<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\User;
use App\Domain\Users\PasswordValidationRules;
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
     * @param  array<string, mixed>  $input
     */
    public function reset(mixed $user, array $input): void
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
