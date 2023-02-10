<?php

declare(strict_types=1);

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Models\Chat;
use App\Domain\Users\Models\User;
use App\Http\Middleware\InjectClientId;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetInterlocutor
{
    use AsAction;

    public function handle(User $user): Collection
    {
        return $user->client->getUsersWithPermissionQuery(['chat.create'], Chat::class)
            ->filter(static fn (User $u) => $u->id !== $user->id);
    }

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        /** @var User $current_user */
        $current_user = $request->user();

        /// This was really over-engineered
        return $current_user->can('chat.create', Chat::class);
    }

    public function asController(ActionRequest $request): Collection
    {
        return $this->handle($request->user());
    }

    /**
     * @param Collection<User> $collection
     *
     */
    public function jsonResponse(Collection $collection): JsonResponse
    {
        return new JsonResponse($collection->map->only(['id', 'name']));
    }
}
