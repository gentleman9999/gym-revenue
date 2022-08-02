<?php

namespace App\Domain\Templates\EmailTemplates;

use App\Domain\Templates\EmailTemplates\Actions\GenerateEmailTemplateThumbnail;
use App\Domain\Templates\EmailTemplates\Events\EmailTemplateCreated;
use App\Domain\Templates\EmailTemplates\Events\EmailTemplateUpdated;
use function env;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class EmailTemplateReactor extends Reactor implements ShouldQueue
{
    public function onEmailTemplateCreated(EmailTemplateCreated $event)
    {
        $this->generateThumbnail($event->aggregateRootUuid(), $event->payload['markup']);
    }

    public function onEmailTemplateUpdated(EmailTemplateUpdated $event)
    {
        if (array_key_exists('markup', $event->payload)) {
            $this->generateThumbnail($event->aggregateRootUuid(), $event->payload['markup']);
        }
    }

    public function generateThumbnail($id, $html)
    {
        if (env('GENERATE_THUMBNAILS', true)) {
            GenerateEmailTemplateThumbnail::dispatch($id, $html);
        }
    }
}
