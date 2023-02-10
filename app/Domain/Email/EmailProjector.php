<?php

declare(strict_types=1);

namespace App\Domain\Email;

use App\Domain\Email\Events\EmailLog;
use App\Domain\Email\Events\MailgunTracked;
use App\Models\ClientEmailLog;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class EmailProjector extends Projector
{
    public function onMailgunTracked(MailgunTracked $event): void
    {
        info('test mailgun tracked: ', $event->payload);
        $payload = [];
        switch ($event->payload['event']) {
            case 'accepted':
                $payload = ['accepted_at' => $event->payload['timestamp']];

                break;

            case 'sent':
                $payload = ['sent_at' => $event->payload['timestamp']];

                break;

            case 'delivered':
                $payload = ['delivered_at' => $event->payload['timestamp']];

                break;

            case 'failed':
                $payload = ['failed_at' => $event->payload['timestamp']];

                break;

            case 'opened':
                $payload = ['opened_at' => $event->payload['timestamp']];

                break;
            default:
                $x = 1;
        }

        $ClientEmailLog = ClientEmailLog::whereMessageId($event->payload['message']['headers']['message-id'])->first();

        if ($ClientEmailLog) {
            ClientEmailLog::findOrFail($ClientEmailLog->id)->writeable()->updateOrFail($payload);
        }
    }

    public function onEmailLog(EmailLog $event): void
    {
        $email_tracking = (new ClientEmailLog())->writeable();
        //get only the keys we care about (the ones marked as fillable)
        $fillable_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new ClientEmailLog())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $email_tracking->fill($fillable_data);
        $email_tracking->save();
    }
}
