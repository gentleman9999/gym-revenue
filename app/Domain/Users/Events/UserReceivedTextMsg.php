<?php

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevShouldBeStored;

class UserReceivedTextMsg extends GymRevShouldBeStored
{
    public $template;
    public $response;
    public $client;

    public function __construct(string $template, string $response, $client = null)
    {
        $this->template = $template;
        $this->response = $response;
        $this->client = $client;
    }
}
