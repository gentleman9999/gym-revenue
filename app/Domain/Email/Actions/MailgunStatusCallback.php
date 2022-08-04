<?php

namespace App\Domain\Email\Actions;

use App\Support\Uuid;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class MailgunStatusCallback
{
    use AsAction;

    public function handle($payload)
    {
        $id = Uuid::new();
        //SmsAggregate::retrieve($id)->twilioTrack($payload)->persist();


        foreach ($payload as $i => $k) {
            info('Key '. $i);
            info('Data ', $k);
        }


        return $payload;
    }

    public function asController(ActionRequest $request)
    {
        info("testing mailgun callback: ");

        return $this->handle($request->all());
    }

    public function jsonResponse($result)
    {
        return response('success', 200);
    }
}
