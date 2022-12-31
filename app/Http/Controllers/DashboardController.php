<?php

namespace App\Http\Controllers;

use App\Domain\Clients\Projections\Client;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\User;
use App\Enums\SecurityGroupEnum;
use App\Enums\UserTypesEnum;
use App\Services\Dashboard\HomeDashboardService;
use App\Support\CurrentInfoRetriever;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected Client $clients;
    protected HomeDashboardService $service;

    public function __construct(Client $clients, HomeDashboardService $service)
    {
        $this->clients = $clients;
        $this->service = $service;
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->user_type == UserTypesEnum::EMPLOYEE) {
            $view_details = $this->clientUserDashboard($user);
        } else {
            $view_details = [
                'view_name' => '',
                'view_data' => [
                    'accountName' => $user->name,
                    'announcements' => [],
                    ],
                ];
        }

        return Inertia::render(
            $view_details['view_name'],
            $view_details['view_data'],
        );
    }

    protected function clientUserDashboard(User $user): array
    {
        $team = CurrentInfoRetriever::getCurrentTeam();
        $client = $team->client;
        $announcements = [];
        $team_name = $team->name;
        $vue = 'Dashboard';
        $widgets = $this->service->getDashboardWidgets();
        if ($client !== null) {
            $clients = collect([$client]);
            $account = $client->name;
            if ($user->inSecurityGroup(SecurityGroupEnum::ADMIN, SecurityGroupEnum::ACCOUNT_OWNER, SecurityGroupEnum::REGIONAL_ADMIN)) {
                $vue = 'Dashboards/AccountAdminDashboard';
            } elseif ($user->inSecurityGroup(SecurityGroupEnum::LOCATION_MANAGER)) {
                $vue = 'Dashboards/LocationManagerDashboard';
            } else {
                $vue = 'Dashboards/SalesRepDashboard';
            }

            return [
                'view_name' => $vue,
                'view_data' => [
                    'teamName' => $team_name,
                    'clients' => $clients,
                    'accountName' => $account,
                    'widgets' => $widgets,
                    'announcements' => $announcements,
                ],
            ];
        } else {
            $account = 'GymRevenue';
            $clients = $this->clients->all();
            $announcements = $this->service->getAppStateAnnouncements();

            // Check if this is the CnB Default team.
            // If so the vue will be AdminDashboard. else DeveloperDashboard
            $vue = ($team->home_team) ? 'Dashboards/AdminDashboard' : 'Dashboards/DeveloperDashboard';
            $teams = $user->allTeams()->load('client');

            if (count($clients) > 0) {
                $clients = $clients->toArray();
                foreach ($clients as $idx => $client) {
                    $clients[$idx]['created_at'] = date('M d, Y', strtotime($client['created_at']));
                    $clients[$idx]['updated_at'] = date('M d, Y', strtotime($client['updated_at']));
                }
            }

            return [
                'view_name' => $vue,
                'view_data' => [
                    'teamName' => $team_name,
                    'teams' => $teams,
                    'clients' => $clients,
                    'accountName' => $account,
                    'widgets' => $widgets,
                    'announcements' => $announcements,
                ],
            ];
        }
    }
}
