<?php

namespace App\Projectors\Clients;

use App\Models\ShortUrl;
use App\StorableEvents\Clients\ShortUrl\ShortUrlCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ShortUrlProjector extends Projector
{
    public function onShortUrlCreated(ShortUrlCreated $event)
    {
        ShortUrl::create($event->data);
    }
}
