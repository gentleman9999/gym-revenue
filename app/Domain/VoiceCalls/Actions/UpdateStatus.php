<?php

declare(strict_types=1);

namespace App\Domain\VoiceCalls\Actions;

use App\Domain\Users\Models\User;
use App\Domain\VoiceCalls\Models\ClientVoiceCallLog;
use App\Models\GatewayProviders\GatewayProvider;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateStatus
{
    use AsAction;

    public function handle(User $user, GatewayProvider $gateway, array $status): array
    {
        ClientVoiceCallLog::add([
            'user_id' => $user->id,
            'gateway_id' => $gateway->id,
            'conversation_id' => $status['CallSid'],
            'status' => $status['CallStatus'],
            'to' => $status['To'],
            // @see https://www.twilio.com/docs/voice/make-calls?code-sample=code-make-an-outbound-call-to-a-phone-number&code-language=PHP&code-sdk-version=6.x
            'payload' => $status,
        ]);

        return ['msg' => 'success'];
    }

    public function asController(User $user, GatewayProvider $gateway, ActionRequest $request): array
    {
        return $this->handle($user, $gateway, $request->toArray());
    }

    public function jsonResponse(array $response): JsonResponse
    {
        return new JsonResponse($response);
    }
}
