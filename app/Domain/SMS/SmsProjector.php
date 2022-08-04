<?php

namespace App\Domain\SMS;

use App\Domain\SMS\Events\TwilioTracked;
use App\Domain\SMS\Models\TwilioCallback;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class SmsProjector extends Projector
{
    public function onTwilioTracked(TwilioTracked $event): void
    {
        $sms_tracking = new TwilioCallback();
        //get only the keys we care about (the ones marked as fillable)
        $fillable_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new TwilioCallback())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $sms_tracking->fill($fillable_data);
        $sms_tracking->save();
    }
}
