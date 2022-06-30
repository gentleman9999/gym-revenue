<?php

namespace App\Reactors\Clients;

use App\Actions\Clients\Activity\Comms\GenerateEmailTemplateThumbnail;
use App\StorableEvents\Clients\Comms\EmailTemplateCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class EmailTemplateReactor extends Reactor implements ShouldQueue
{
    public function onEmailTemplateCreated(EmailTemplateCreated $event)
    {
        $this->generateThumbnail($event->data['id'], $event->data['markup']);
    }

    public function onEmailTemplateUpdated(EmailTemplateUpdated $event)
    {
        if (array_key_exists('markup', $event->data)) {
            $this->generateThumbnail($event->data['id'], $event->data['markup']);
        }
    }

    public function generateThumbnail($id, $html)
    {
        if (env('GENERATE_THUMBNAILS', true)) {
            GenerateEmailTemplateThumbnail::dispatch($id, $html);
        }
    }
}
