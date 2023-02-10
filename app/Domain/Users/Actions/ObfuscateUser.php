<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Models\User;
use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ObfuscateUser
{
    use AsAction;

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        //TODO temp change when we figure out the bouncer abilities
        return $request->user()->can('users.edit', User::class);
    }

    public function asController(User $user): User
    {
        return $this->handle($user);
    }

    public function handle(User $user): User
    {
        UserAggregate::retrieve($user->id)->obfuscateUser()->persist();

        return $user->refresh();
    }
}
