<?php

namespace App\Domain\Users\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserSetCustomCrudColumns extends ShouldBeStored
{
    public $table;
    public $fields;

    public function __construct(string $table, array $fields)
    {
        $this->table = $table;
        $this->fields = $fields;
    }
}
