<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplates;

use App\Domain\Templates\EmailTemplates\Actions\GenerateEmailTemplateThumbnail;
use App\Domain\Templates\EmailTemplates\Events\EmailTemplateCreated;
use App\Domain\Templates\EmailTemplates\Events\EmailTemplateUpdated;

use function env;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class EmailTemplateReactor extends Reactor implements ShouldQueue
{
    public function onEmailTemplateCreated(EmailTemplateCreated $event): void
    {
        $this->generateThumbnail($event->aggregateRootUuid(), $event->payload['markup']);
    }

    public function onEmailTemplateUpdated(EmailTemplateUpdated $event): void
    {
        if (array_key_exists('markup', $event->payload)) {
            $this->generateThumbnail($event->aggregateRootUuid(), $event->payload['markup']);
        }
    }

    public function generateThumbnail($id, $html): void
    {
        if (env('GENERATE_THUMBNAILS', true)) {
            GenerateEmailTemplateThumbnail::dispatch($id, $html);
        }
    }
}
