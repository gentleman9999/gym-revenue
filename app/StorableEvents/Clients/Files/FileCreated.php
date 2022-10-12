<?php

declare(strict_types=1);

namespace App\StorableEvents\Clients\Files;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class FileCreated extends ShouldBeStored
{
    public function __construct(public ?string $user = null, public array $data = [])
    {
    }
}
