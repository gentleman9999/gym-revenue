<?php

namespace App\GraphQL\Queries;

use App\Domain\Clients\Projections\Client;
use App\Domain\Teams\Models\Team;
use App\Services\Dashboard\HomeDashboardService;

final class DashboardQuery
{
    protected Client $clients;
    protected HomeDashboardService $service;

    public function __construct(Client $clients, HomeDashboardService $service)
    {
        $this->clients = $clients;
        $this->service = $service;
    }

    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
        $user = auth()->user();
        $session_team = session()->get('current_team');
        if ($session_team && array_key_exists('id', $session_team)) {
            $team = Team::find($session_team['id']);
        } else {
            $team = Team::find($user->default_team_id);
        }
        $client = $team->client;
        $announcements = [];
        $team_name = $team->name;
        if ($client !== null) {
            $clients = collect([$client]);
            $account = $client->name;

            return [
                'teamName' => $team_name,
                'accountName' => $account,
                // 'clients' => $clients,
                // 'announcements' => $announcements,
            ];
        } else {
            $account = 'GymRevenue';
            $clients = $this->clients->all();
            $announcements = $this->service->getAppStateAnnouncements();

            $teams = $user->allTeams()->load('client');

            if (count($clients) > 0) {
                $clients = $clients->toArray();
                foreach ($clients as $idx => $client) {
                    $clients[$idx]['created_at'] = date('M d, Y', strtotime($client['created_at']));
                    $clients[$idx]['updated_at'] = date('M d, Y', strtotime($client['updated_at']));
                }
            }

            return [
                'accountName' => $account,
                'teamName' => $team_name,
                // 'teams' => $teams,
                // 'clients' => $clients,
                // 'announcements' => $announcements,
            ];
        }
    }
}
