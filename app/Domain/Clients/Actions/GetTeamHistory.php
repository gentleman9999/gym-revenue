<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\Queries\TeamHistory;
use App\Domain\Teams\Models\Team;
use Lorisleiva\Actions\Concerns\AsAction;

class GetTeamHistory
{
    use AsAction;

    public function handle(string $client_id): Team
    {
        $history = new TeamHistory($client_id);
        dd($history->getHistory());
    }
}
