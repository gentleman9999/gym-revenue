<?php

declare(strict_types=1);

namespace App\Domain\VoiceCalls\Actions;

use App\Domain\Users\Models\User;
use App\Services\GatewayProviders\Voice\VoiceGatewayProviderService;
use Illuminate\Console\Command;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Twilio\Rest\Api\V2010\Account\CallInstance;
use function auth;

class GetStatus
{
    use AsAction;

    public string $commandSignature = 'call:get_call {caller_id} {sid}';
    public string $commandDescription = 'Gets a call status for specified sid';

    public function handle(User $caller, string $sid): CallInstance
    {
        return (new VoiceGatewayProviderService())->initVoiceGateway($caller)->getCallStatus($sid);
    }

    public function asController(ActionRequest $request): CallInstance
    {
        return $this->handle(auth()->user(), $request->sid);
    }

    public function jsonResponse(CallInstance $result): JsonResponse
    {
        return new JsonResponse($result->toArray());
    }

    public function asCommand(Command $command): void
    {
        $user = User::findOrFail($command->argument('caller_id'));

        $this->handle($user, $command->argument('sid'));
    }
}
