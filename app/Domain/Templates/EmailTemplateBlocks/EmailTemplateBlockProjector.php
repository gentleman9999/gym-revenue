<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplateBlocks;

use App\Domain\Templates\EmailTemplateBlocks\Events\EmailTemplateBlockCreated;
use App\Domain\Templates\EmailTemplateBlocks\Events\EmailTemplateBlockDeleted;
use App\Domain\Templates\EmailTemplateBlocks\Events\EmailTemplateBlockUpdated;
use App\Domain\Templates\EmailTemplateBlocks\Models\EmailTemplateBlock;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class EmailTemplateBlockProjector extends Projector
{
    public function onEmailTemplateBlockCreated(EmailTemplateBlockCreated $event): void
    {
        $block            = new EmailTemplateBlock();
        $block->id        = $event->aggregateRootUuid();
        $block->client_id = $event->clientId();
        $block->user_id   = $event->userId();

        $block->fill($event->payload);
        $block->writeable()->save();
    }

    public function onEmailTemplateBlockUpdated(EmailTemplateBlockUpdated $event): void
    {
        EmailTemplateBlock::findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail($event->payload);
    }

    public function onEmailTemplateBlockDeleted(EmailTemplateBlockDeleted $event): void
    {
        EmailTemplateBlock::findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }
}
