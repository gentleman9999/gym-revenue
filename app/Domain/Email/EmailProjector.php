<?php

namespace App\Domain\Email;

use App\Domain\Email\Events\EmailLog;
use App\Domain\Email\Events\MailgunTracked;
use App\Models\ClientEmailLog;
use Carbon\Carbon;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class EmailProjector extends Projector
{
    public function onMailgunTracked(MailgunTracked $event): void
    {
        info('test mailgun tracked: ', $event->payload);
        $payload = [];
        switch ($event->payload['event']) {
            case 'accepted':
                $payload = ['accepted_at' => Carbon::now()];

                break;

            case 'sent':
                $payload = ['sent_at' => Carbon::now()];

                break;

            case 'delivered':
                $payload = ['delivered_at' => Carbon::now()];

                break;

            case 'failed':
                $payload = ['failed_at' => Carbon::now()];

                break;

            case 'opened':
                $payload = ['opened_at' => Carbon::now()];

                break;
            default:
                $x = 1;
        };

        $ClientEmailLog = ClientEmailLog::whereMessageId($event->payload['message']['headers']['message-id'])->first();

        ClientEmailLog::findOrFail($ClientEmailLog->id)->writeable()->updateOrFail($payload);
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
