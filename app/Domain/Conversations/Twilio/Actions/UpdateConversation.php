<?php

declare(strict_types=1);

namespace App\Domain\Conversations\Twilio\Actions;

use App\Domain\Conversations\Twilio\Models\ClientConversation;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateConversation
{
    use AsAction;

    public function handle(ClientConversation $client_conversation): void
    {
        // We just want to update the updated_at when a new chat is received/sent
        $client_conversation->touch();
    }

    /**
     *
     */
    public function asController(ActionRequest $request, string $conversation_id): void
    {
        $this->handle(ClientConversation::where(['conversation_id' => $conversation_id])->first());
    }

    public function jsonResponse(): JsonResponse
    {
        return new JsonResponse(['status' => 'success']);
    }
}
