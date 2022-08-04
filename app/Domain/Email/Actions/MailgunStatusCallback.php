<?php

namespace App\Domain\Email\Actions;

use App\Support\Uuid;
use hollodotme\FastCGI\RequestContents\JsonData;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class MailgunStatusCallback
{
    use AsAction;

    public function handle(JsonData $payload)
    {
        $id = Uuid::new();
        //SmsAggregate::retrieve($id)->twilioTrack($payload)->persist();

        info("testing mailgun callback: ". json_decode($payload));

        return $payload;
    }

    public function asController(ActionRequest $request)
    {
        info("testing mailgun callback: ");

        return $this->handle($request->json());
    }

    public function jsonResponse($result)
    {
        return response('success', 200);
    }
}
