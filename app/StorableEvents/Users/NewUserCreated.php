<?php

namespace App\StorableEvents\Users;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class NewUserCreated extends ShouldBeStored
{
    public $id, $data;
    public function __construct($id, $data)
    {
        $this->id = $id;
        $this->data = $data;
    }
}
