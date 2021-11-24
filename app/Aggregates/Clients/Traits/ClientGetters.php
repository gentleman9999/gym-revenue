<?php

namespace App\Aggregates\Clients\Traits;

trait ClientGetters
{
    protected $comm_history = [];

    public function getCommunicationHistoryLog() : array
    {
        $results = [];
        if(count($this->comm_history) > 0)
        {
            $results = $this->comm_history;
        }

        return $results;
    }
}
