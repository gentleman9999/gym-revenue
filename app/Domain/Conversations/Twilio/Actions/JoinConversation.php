<?php

declare(strict_types=1);

namespace App\Domain\Conversations\Twilio\Actions;

use App\Domain\Conversations\Twilio\ClientConversationAggregates;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Ramsey\Uuid\Uuid;

class JoinConversation
{
    use AsAction;

    public function handle(string $client_id, string $conversation_sid, string $participant_sid, ?string $sender): void
    {
        $uuid = (string) Uuid::uuid4();
        ClientConversationAggregates::retrieve($uuid)->clientConversationJoined([
            'client_id' => $client_id,
            'conversation_id' => $conversation_sid,
            'participant_id' => $participant_sid,
            'sender' => $sender,
        ])->persist();
    }

    public function asController(ActionRequest $request, string $client_id)
    {
        // https://www.twilio.com/docs/conversations/conversations-webhooks#onmessageadd
        $this->handle(
            $client_id,
            $request->post('ConversationSid'),
            $request->post('ParticipantSid'),
            // If client is the one initializing conversation, look into "attributes"
            $request->post('Author'),
        );
    }
}
