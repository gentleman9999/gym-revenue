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

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        //TODO temp change when we figure out the bouncer abilities
        return $current_user->can('users.edit', User::class);
    }

    public function asController(ActionRequest $request, User $user): User
    {
        return $this->handle(
            $user
        );
    }

    public function handle(User $user): User
    {
        UserAggregate::retrieve(strval($user->id))->ObfuscateUser()->persist();
        ReflectUserData::run($user);

        return $user->refresh();
    }
}
