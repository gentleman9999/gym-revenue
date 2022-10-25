<?php

namespace App\GraphQL\Queries;

use App\Domain\Teams\Models\Team;

final class LeadOwners
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
        $client_id = request()->user()->client_id;
        $session_team = session()->get('current_team');
        if ($session_team && array_key_exists('id', $session_team)) {
            $current_team = Team::find($session_team['id']);
        } else {
            $current_team = Team::find($user->default_team_id);
        }
        $team_users = $current_team->team_users()->get();

        return $team_users;
    }
}
