<?php

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevShouldBeStored;

class UserSetCustomCrudColumns extends GymRevShouldBeStored
{
    public $table;
    public $fields;

    public function __construct(string $table, array $fields)
    {
        $this->table = $table;
        $this->fields = $fields;
    }
}
