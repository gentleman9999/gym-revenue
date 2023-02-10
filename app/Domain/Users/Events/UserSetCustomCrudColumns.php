<?php

declare(strict_types=1);

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevShouldBeStored;

class UserSetCustomCrudColumns extends GymRevShouldBeStored
{
    /**
     * @param string $table
     * @param array<string>  $fields
     */
    public function __construct(public string $table, public array $fields)
    {
    }
}
