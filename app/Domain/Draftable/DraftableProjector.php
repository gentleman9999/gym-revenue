<?php

declare(strict_types=1);

namespace App\Domain\Draftable;

use App\Domain\Draftable\Events\DraftableCreated;
use CapeAndBay\Draftable\Traits\DraftableModel;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class DraftableProjector extends Projector
{
    public function onDraftableCreated(DraftableCreated $event): void
    {
        /** @var DraftableModel $model */
        $model = $event->payload['model'];
        $owner = $event->payload['owner'];

        $model::setOwner($owner);
        $model->saveAsDraft();
        $model->draft->setData('uuid', $event->aggregateRootUuid());
    }
}
