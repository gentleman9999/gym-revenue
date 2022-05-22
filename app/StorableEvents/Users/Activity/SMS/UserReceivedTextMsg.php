<?php

namespace App\StorableEvents\Users\Activity\SMS;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserReceivedTextMsg extends ShouldBeStored
{
    public $user;
    public $template;
    public $response;
    public $client;

    public function __construct($user, $template, $response, $client = null)
    {
        $this->user = $user;
        $this->template = $template;
        $this->response = $response;
        $this->client = $client;
    }
}
