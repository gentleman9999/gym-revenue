<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Domain\Teams\Models\Team;
use App\Domain\Teams\Models\TeamUser;
use Illuminate\Support\Collection;

final class LeadOwners
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $_args
     *
     * @return Collection<TeamUser>
     */
    public function __invoke($_, array $_args): Collection
    {
        // TODO implement the resolver
        $session_team = session()->get('current_team');
        if ($session_team && array_key_exists('id', $session_team)) {
            $current_team = Team::find($session_team['id']);
        } else {
            $current_team = Team::find(request()->user()->default_team_id);
        }
        return $current_team->team_users()->get();
    }
}
