<?php

declare(strict_types=1);

namespace App\Domain\Conversations\Twilio\Actions;

use App\Domain\Conversations\Twilio\Exceptions\ConversationException;
use App\Domain\Users\Models\User;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Jwt\Grants\ChatGrant;

class GetChatAccessToken
{
    use AsAction;

    /**
     * @throws ConfigurationException
     * @throws ConversationException
     */
    public function handle(User $user): array
    {
        $client = $user->client;
        $twilio_service = $client->getTwilioService();
        // https://www.twilio.com/docs/iam/access-tokens#create-an-access-token-for-conversations
        $chat_grant = new ChatGrant();
        $chat_grant->setServiceSid($twilio_service->conversation_service_sid);

        return $twilio_service->getAccessToken($user, [$chat_grant]);
    }

    /**
     * @param ActionRequest $request
     *
     * @return array
     * @throws ConfigurationException
     * @throws ConversationException
     */
    public function asController(ActionRequest $request): array
    {
        return $this->handle($request->user());
    }

    public function jsonResponse(array $response): JsonResponse
    {
        return new JsonResponse($response);
    }
}
