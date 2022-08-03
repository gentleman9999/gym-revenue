<?php

namespace App\Domain\SMS;

use App\Domain\SMS\Events\SMSTracked;
use App\Models\SmsTracking;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class SmsProjector extends Projector
{
    public function onSmsTracked(SMSTracked $event): void
    {
        $sms_tracking = new SmsTracking();
        //get only the keys we care about (the ones marked as fillable)
        $fillable_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new SmsTracking())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $sms_tracking->fill($fillable_data);
        $sms_tracking->id = $event->aggregateRootUuid();
        $sms_tracking->client_id = $event->payload['client_id'] ?? null;
        $sms_tracking->save();
    }
}
