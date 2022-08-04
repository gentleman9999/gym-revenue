<?php

namespace App\Domain\Email\Actions;

use App\Domain\Email\EmailAggregate;
use App\Support\Uuid;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class MailgunStatusCallback
{
    use AsAction;

    public function handle($payload)
    {
        $id = Uuid::new();

        foreach ($payload as $i => $k) {
            info('Key '. $i);
            info('Data ', $k);
            if ($i == "event-data") {
                $k['MailgunId'] = $k['id'];
                $k['timestamp'] = date('Y-m-d H:i:s', $k['timestamp']);
                $k['MessageId'] = $k['message']['headers']['message-id'];
                if ($k['event'] == 'delivered') {
                    $k['sender'] = $k['envelope']['sender'];
                    $k['SenderIpAddress'] = $k['envelope']['sending-ip'];
                }
                EmailAggregate::retrieve($id)->mailgunTrack($k)->persist();
            }
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
