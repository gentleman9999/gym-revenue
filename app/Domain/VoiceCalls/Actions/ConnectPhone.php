<?php

declare(strict_types=1);

namespace App\Domain\VoiceCalls\Actions;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Twilio\TwiML\VoiceResponse;

class ConnectPhone
{
    use AsAction;

    public function handle(string $phone, string $answered_by): string
    {
        // https://www.twilio.com/docs/voice/twiml/dial
        $response = new VoiceResponse();

        // https://www.twilio.com/docs/voice/answering-machine-detection
        // If call was decline/answered by machine
        if ($answered_by === 'machine_start') {
            $response->reject();
        } else {
            // $response->say('Connecting Lead');
            $response->dial($phone);
        }

        return (string) $response;
    }

    public function asController(ActionRequest $request): string
    {
        return $this->handle($request->phone, $request->get('AnsweredBy'));
    }
}
