<?php

declare(strict_types=1);

namespace App\Projectors\Clients;

use App\Models\ShortUrl;
use App\StorableEvents\Clients\ShortUrl\ShortUrlCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ShortUrlProjector extends Projector
{
    public function onShortUrlCreated(ShortUrlCreated $event): void
    {
        ShortUrl::create($event->data);
    }
}
