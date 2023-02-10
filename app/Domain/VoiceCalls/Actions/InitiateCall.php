<?php

declare(strict_types=1);

namespace App\Domain\VoiceCalls\Actions;

use App\Domain\Users\Models\User;
use App\Services\GatewayProviders\Voice\VoiceGatewayProviderService;

use function auth;

use Illuminate\Console\Command;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Twilio\Rest\Api\V2010\Account\CallInstance;

class InitiateCall
{
    use AsAction;

    public const TYPE_LEAD            = 'lead';
    public const TYPE_MEMBER          = 'member';
    public string $commandSignature   = 'call:init {caller_id} {recipient} {type}';
    public string $commandDescription = 'Initiates a call from the provided user id to the provided recipient number';

    /**
     * @param string $type Type of call. e.g. lead|member
     *
     */
    public function handle(User $caller, string $number_to_dial, string $type): CallInstance
    {
        return (new VoiceGatewayProviderService())->initVoiceGateway($caller)->call($number_to_dial);
    }

    public function asController(ActionRequest $request): CallInstance
    {
        return $this->handle(auth()->user(), $request->phone, $request->type);
    }

    public function jsonResponse(CallInstance $result): JsonResponse
    {
        return new JsonResponse($result->toArray());
    }

//    public function htmlResponse(CallInstance $call): RedirectResponse
//    {
//        //this only gets called if our App calls via InertiaRequest.
//    }

    //command for ez development testing
    public function asCommand(Command $command): void
    {
        $user = User::findOrFail($command->argument('caller_id'));

        $this->handle($user, $command->argument('recipient'), $command->argument('type'));
    }
}
