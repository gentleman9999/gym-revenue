<?php

namespace App\Aggregates\Clients\Traits;

use App\Models\User;

trait ClientGetters
{
    protected $comm_history = [];
    protected $provider_history = [];

    public function getCommunicationHistoryLog() : array
    {
        $results = [];
        if(count($this->comm_history) > 0)
        {
            $comm_history = collect($this->comm_history)->sortByDesc('date');
            foreach ($comm_history->toArray() as $idx => $history)
            {
                if($history['by'] != 'Auto Generated')
                {
                    $user = User::find($history['by']);
                    $history['by'] = $user->name;
                }

                if(!array_key_exists('model', $history))
                {
                    $history['recordName'] = $history['name'];
                }
                else
                {
                    if(array_key_exists('template_id', $history))
                    {
                        $model = $history['model']::find($history['template_id']);
                    }
                    elseif(array_key_exists('campaign_id', $history))
                    {
                        $model = $history['model']::find($history['campaign_id']);
                    }

                    if(!empty($model)){
                        $history['recordName'] = $model->name;
                    }
                }

                $results[] = $history;
            }
        }

        return $results;
    }
}
