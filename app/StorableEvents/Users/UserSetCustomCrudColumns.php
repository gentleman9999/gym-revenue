<?php

namespace App\StorableEvents\Users;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserSetCustomCrudColumns extends ShouldBeStored
{
    public $user;
    public $table;
    public $fields;

    public function __construct(string $user, string $table, array $fields)
    {
        $this->user = $user;
        $this->table = $table;
        $this->fields = $fields;
    }
}
