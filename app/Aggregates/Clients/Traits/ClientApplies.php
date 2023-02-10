<?php

declare(strict_types=1);

namespace App\Aggregates\Clients\Traits;

use App\StorableEvents\Clients\Activity\Users\ClientUserStoppedBeingImpersonated;
use App\StorableEvents\Clients\Activity\Users\ClientUserWasImpersonated;

trait ClientApplies
{
    protected string $date_format = 'Y-m-d H:i:s';

    public function applyClientUserWasImpersonated(ClientUserWasImpersonated $event): void
    {
        $this->employee_activity[] = [
            'event' => 'user-was-impersonated',
            'employee' => $event->employee,
            'date' => $event->date,
            'impersonator' => $event->invader,
        ];
    }

    public function applyClientUserStoppedBeingImpersonated(ClientUserStoppedBeingImpersonated $event): void
    {
        $this->employee_activity[] = [
            'event' => 'user-stopped-being-impersonated',
            'employee' => $event->employee,
            'date' => $event->date,
            'impersonator' => $event->invader,
        ];
    }
}
