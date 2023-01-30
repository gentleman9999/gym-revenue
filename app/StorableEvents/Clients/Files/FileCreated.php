<?php

declare(strict_types=1);

namespace App\StorableEvents\Clients\Files;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

//TODO: move all of the StorableEvents to their respective domain folders.
class FileCreated extends ShouldBeStored
{
    public function __construct(public object $model, public ?string $user = null, public array $data = [])
    {
    }
}
