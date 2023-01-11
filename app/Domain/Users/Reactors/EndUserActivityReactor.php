<?php

declare(strict_types=1);

namespace App\Domain\Users\Reactors;

use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Events\EndUserFileUploaded;
use App\Domain\Users\Events\EndUserWasEmailedByRep;
use App\Domain\Users\Models\EndUser;
use App\Domain\Users\Models\User;
use App\Mail\EndUser\EmailFromRep;
use App\Models\Utility\AppState;


use Illuminate\Support\Facades\Mail;

class EndUserActivityReactor extends BaseEndUserReactor
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

    public function onEndUserFileUploaded(EndUserFileUploaded $event): void
    {
        $data = $event->payload;
        $model = EndUser::find($data['entity_id']);
        $user = User::find($event->userId());

        \App\Actions\Clients\Files\CreateFile::run($data, $model, $user);
    }

    public static function getModel(): EndUser
    {
        return new EndUser();
    }

    public static function getAggregate(): UserAggregate
    {
        return new UserAggregate();
    }
}
