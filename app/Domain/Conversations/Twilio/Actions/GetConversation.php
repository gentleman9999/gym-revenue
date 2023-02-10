<?php

declare(strict_types=1);

namespace App\Domain\Conversations\Twilio\Actions;

use App\Domain\Users\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetConversation
{
    use AsAction;

    public function handle(User $user): Collection
    {
        return $user->twilioClientConversation()->latest('updated_at')->get();
    }

    /**
     *
     */
    public function asController(ActionRequest $request): Collection
    {
        return $this->handle($request->user());
    }

    public function jsonResponse(Collection $collection): JsonResponse
    {
        return new JsonResponse($collection);
    }
}
