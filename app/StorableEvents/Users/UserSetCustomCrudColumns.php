<?php

namespace App\StorableEvents\Users;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserSetCustomCrudColumns extends ShouldBeStored
{
    public $user, $table, $fields;

    public function __construct($user, string $table, array $fields)
    {
        $this->user = $user;
        $this->table = $table;
        $this->fields = $fields;
    }
}
