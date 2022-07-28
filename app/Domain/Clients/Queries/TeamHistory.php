<?php

namespace App\Domain\Clients\Queries;

use App\Domain\Teams\Events\TeamCreated;
use App\Domain\Teams\Events\TeamUpdated;
use App\Domain\Teams\Models\Team;
use App\StorableEvents\GymRevStoredEvent;
use Spatie\EventSourcing\EventHandlers\Projectors\EventQuery;

//use Spatie\Period\Period;

//Example of using event queries to generate client specific aggregates
class TeamHistory extends EventQuery
{
    public function __construct(
        private string $client_id,
//        private Period $period,
    ) {
        GymRevStoredEvent::query()
            ->where('meta_data->client_id', $client_id)
            ->where('meta_data->entity', Team::class)
// if we only need events within a given period
//            ->whereDate(
//                'created_at', '>=', $this->period->getStart()
//            )
//            ->whereDate(
//                'created_at', '<=', $this->period->getEnd()
//            )
//                ->get()
            ->each(
                fn (GymRevStoredEvent $event) => $this->apply($event->toStoredEvent())
            );
    }

    protected $teams = [];
    protected $history = [];

    protected function applyTeamCreated(TeamCreated $event): void
    {
        $this->teams[$event->aggregateRootUuid()] = $event->payload;
        if ($event->autoGenerated()) {
            $created_by = "was Auto Generated";
        } elseif ($event->accessToken()) {
            $created_by = "was created via API";
        } elseif ($event->userId()) {
            $created_by = "was created by {$event->user()->name}";
        }
        $this->history[] = "Team {$event->payload['name']} $created_by ";
    }

    protected function applyTeamUpdated(TeamUpdated $event): void
    {
        $this->teams[$event->aggregateRootUuid()] = array_merge($this->teams[$event->aggregateRootUuid()], $event->payload);

        if ($event->accessToken()) {
            $updated_by = "was updated via API";
        } elseif ($event->userId()) {
            $updated_by = "was updated by {$event->user()->name}";
        }
        $this->history[] = "Team {$this->teams[$event->aggregateRootUuid()]} $updated_by ";
    }

    public function getTeams(): array
    {
        return $this->teams;
    }

    public function getHistory(): array
    {
        return $this->history;
    }
}
