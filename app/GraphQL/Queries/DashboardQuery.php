<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Domain\Clients\Projections\Client;
use App\Domain\Teams\Models\Team;
use App\Services\Dashboard\HomeDashboardService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
     * @param null    $_
     * @param array<string, mixed> $_args
     *
     * @return array<string, mixed>
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke($_, array $_args): array
    {
        // TODO implement the resolver
        $user         = auth()->user();
        $session_team = session()->get('current_team');
        if ($session_team && array_key_exists('id', $session_team)) {
            $team = Team::find($session_team['id']);
        } else {
            $team = Team::find($user->default_team_id);
        }
        $client        = $team->client;
        $announcements = [];
        $team_name     = $team->name;
        $widgets       = $this->service->getDashboardWidgets();
        if ($client !== null) {
            $account = $client->name;

            return [
                'teamName' => $team_name,
                'accountName' => $account,
                'announcements' => $announcements,
                'widgets' => $widgets,
            ];
        } else {
            $account       = 'GymRevenue';
            $announcements = $this->service->getAppStateAnnouncements();

            $teams = $user->allTeams()->load('client');

            return [
                'accountName' => $account,
                'teamName' => $team_name,
                'teams' => $teams,
                'announcements' => $announcements,
                'widgets' => $widgets,
            ];
        }
    }
}
