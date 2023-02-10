<?php

declare(strict_types=1);

namespace App\Domain\Draftable;

use App\Domain\Draftable\Events\DraftableCreated;
use App\Domain\Draftable\Events\DraftableDeleted;
use App\Domain\Draftable\Events\DraftableUpdated;
use CapeAndBay\Draftable\Draftable;
use CapeAndBay\Draftable\Traits\DraftableModel;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class DraftableProjector extends Projector
{
    /**
     * @throws \Exception
     */
    public function onDraftableCreated(DraftableCreated $event): void
    {
        /** @var DraftableModel $model */
        $model = $event->payload['model'];
        $owner = $event->payload['owner'];

        $model->saveAsDraft($owner, $event->aggregateRootUuid());
    }

    /**
     * @throws \Exception
     */
    public function onDraftableUpdated(DraftableUpdated $event): void
    {
        /** @var Draftable $draft */
        $draft = Draftable::find($event->aggregateRootUuid());
        $draft->update(['draftable_data' => $draft->model()->fill($event->payload)->toArray()]);
    }

    public function onDraftableDelete(DraftableDeleted $event): void
    {
        Draftable::where('id', $event->aggregateRootUuid())->delete();
    }
}
