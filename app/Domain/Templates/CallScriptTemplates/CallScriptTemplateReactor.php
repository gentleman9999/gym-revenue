<?php

declare(strict_types=1);

namespace App\Domain\Templates\CallScriptTemplates;

use App\Domain\Templates\CallScriptTemplates\Actions\GenerateCallScriptTemplateThumbnail;
use App\Domain\Templates\CallScriptTemplates\Events\CallScriptTemplateCreated;
use App\Domain\Templates\CallScriptTemplates\Events\CallScriptTemplateUpdated;

use function env;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class CallScriptTemplateReactor extends Reactor implements ShouldQueue
{
    public function onCampaignTemplateCreated(CallScriptTemplateCreated $event): void
    {
        $this->generateThumbnail($event->aggregateRootUuid(), $event->payload['script']);
    }

    public function onCampaignTemplateUpdated(CallScriptTemplateUpdated $event): void
    {
        if (array_key_exists('markup', $event->payload)) {
            $this->generateThumbnail($event->aggregateRootUuid(), $event->payload['script']);
        }
    }

    public function generateThumbnail($id, $html): void
    {
        if (env('GENERATE_THUMBNAILS', true)) {
            GenerateCallScriptTemplateThumbnail::dispatch($id, $html);
        }
    }
}
