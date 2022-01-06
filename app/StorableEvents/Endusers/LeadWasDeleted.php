<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadWasDeleted extends ShouldBeStored
{
	public $lead, $data, $user; 
    public function __construct( $lead, $data, $user )
    {
		$this->lead = $lead; 
		$this->data = $data; 
		$this->user = $user; 
		
    }
}
