<?php

namespace App\Domain\Email;

use App\Domain\Email\Events\MailgunTracked;
use App\Domain\Email\Models\MailgunCallback;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class EmailProjector extends Projector
{
    public function onMailgunTracked(MailgunTracked $event): void
    {
        $email_tracking = new MailgunCallback();
        //get only the keys we care about (the ones marked as fillable)
        $fillable_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new MailgunCallback())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $email_tracking->fill($fillable_data);
        $email_tracking->save();
    }
}
