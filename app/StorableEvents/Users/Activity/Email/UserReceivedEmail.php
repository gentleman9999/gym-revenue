<?php

namespace App\StorableEvents\Users\Activity\Email;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserReceivedEmail extends ShouldBeStored
{
    public $user, $subject, $template, $response, $client;

    public function __construct($user, $subject, $template, $response, $client = null)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->template = $template;
        $this->response = $response;
        $this->client = $client;
    }
}
