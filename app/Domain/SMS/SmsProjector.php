<?php

namespace App\Domain\SMS;

use App\Domain\SMS\Events\SmsLog;
use App\Domain\SMS\Events\TwilioTracked;
use App\Domain\SMS\Models\TwilioCallback;
use App\Models\ClientSmsLog;
use Carbon\Carbon;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class SmsProjector extends Projector
{
    public function onTwilioTracked(TwilioTracked $event): void
    {
        info('test twilio tracked: ', $event->payload);
        $payload = [];
        if ($event->payload['MessageStatus'] == 'sent') {
            $payload = [
                'sent_at' => Carbon::now(),
            ];
        }

        if ($event->payload['MessageStatus'] == 'delivered') {
            $payload = [
                'delivered_at' => Carbon::now(),
            ];
        }
        $ClientSmsLog = ClientSmsLog::whereMessageId($event->payload['SmsSid'])->first();

        ClientSmsLog::findOrFail($ClientSmsLog->id)->writeable()->updateOrFail($payload);

        /*
        $sms_tracking = new TwilioCallback();
        //get only the keys we care about (the ones marked as fillable)
        $fillable_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new TwilioCallback())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $sms_tracking->fill($fillable_data);
        $sms_tracking->save();
        */
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
