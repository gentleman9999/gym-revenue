<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UpdateLead extends NewLeadMade
{
    public $user;

    public function __construct(string $id, array $lead, string $user)
    {
        parent::__construct( $id,  $lead);
        $this->user = $user;
    }
}
