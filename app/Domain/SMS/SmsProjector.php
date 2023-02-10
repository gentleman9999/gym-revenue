<?php

declare(strict_types=1);

namespace App\Domain\SMS;

use App\Domain\SMS\Events\SmsLog;
use App\Domain\SMS\Events\TwilioTracked;
use App\Models\ClientSmsLog;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class SmsProjector extends Projector
{
    public function onTwilioTracked(TwilioTracked $event): void
    {
        info('test twilio tracked: ', $event->payload);
        $payload = [];
        if ($event->payload['MessageStatus'] == 'sent') {
            $payload = [
                'sent_at' => $event->createdAt(),
            ];
        }

        if ($event->payload['MessageStatus'] == 'delivered') {
            $payload = [
                'delivered_at' => $event->createdAt(),
            ];
        }
        $ClientSmsLog = ClientSmsLog::whereMessageId($event->payload['SmsSid'])->first();

        ClientSmsLog::findOrFail($ClientSmsLog->id)->writeable()->updateOrFail($payload);
    }

    public function onSmsLog(SmsLog $event): void
    {
        $sms_tracking = (new ClientSmsLog())->writeable();
        //get only the keys we care about (the ones marked as fillable)
        $fillable_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new ClientSmsLog())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $sms_tracking->fill($fillable_data);
        $sms_tracking->save();
    }
}
