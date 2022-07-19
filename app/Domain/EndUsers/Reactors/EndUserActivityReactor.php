<?php

namespace App\Domain\EndUsers\Reactors;

use App\Actions\Sms\Twilio\FireTwilioMsg;
use App\Domain\EndUsers\Events\EndUserWasEmailedByRep;
use App\Domain\EndUsers\Events\EndUserWasTextMessagedByRep;
use App\Mail\EndUser\EmailFromRep;
use App\Models\Utility\AppState;
use function env;
use Illuminate\Support\Facades\Mail;

abstract class EndUserActivityReactor extends BaseEndUserReactor
{
    public function onEndUserWasEmailedByRep(EndUserWasEmailedByRep $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        $end_user = ($this->getModel())::find($event->aggregateRootUuid());
        if (! AppState::isSimuationMode() && ! $end_user->unsubscribe_comms) {
            Mail::to($end_user->email)->send(new EmailFromRep($event->payload, $event->aggregateRootUuid(), $event->payload['user']));
        }
    }

    public function onEndUserWasTextMessagedByRep(EndUserWasTextMessagedByRep $event): void
    {
        if (($this->getModel())::class !== $event->getEntity()) {
            return;
        }
        $end_user = ($this->getModel())::find($event->aggregateRootUuid());
        $msg = $event->payload['message'];

        if (! AppState::isSimuationMode() && ! $end_user->unsubscribe_comms) {
            FireTwilioMsg::dispatch($end_user->primary_phone, $msg)->onQueue('grp-' . env('APP_ENV') . '-jobs');
        }
    }
}
