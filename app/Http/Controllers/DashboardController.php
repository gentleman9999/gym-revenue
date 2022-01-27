<?php

namespace App\Http\Controllers;

use App\Models\Clients\Client;
use App\Services\Dashboard\HomeDashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected Client $clients;
    protected  HomeDashboardService $service;

    public function __construct(Client $clients, HomeDashboardService $service)
    {
        $this->clients = $clients;
        $this->service = $service;
    }

    public function index()
    {
        $team = auth()->user()->currentTeam;
        $client_detail = $team->client_details()->first();
        $announcements = [];

        if(!is_null($client_detail))
        {
            $clients = collect([$client_detail->client->toArray()]);
            $account = $client_detail->client->name;
            $widgets = $this->service->getDashboardWidgets();
        }
        else {
            $account = 'Cape & Bay';
            $clients = $this->clients->all();
            $widgets = $this->service->getDashboardWidgets();
            $announcements = $this->service->getAppStateAnnouncements();
        }

        if(count($clients) > 0)
        {
            $clients = $clients->toArray();
            foreach($clients as $idx => $client)
            {
                $clients[$idx]['created_at'] = date('M d, Y', strtotime($client['created_at']));
                $clients[$idx]['updated_at'] = date('M d, Y', strtotime($client['updated_at']));
            }
        }

        return Inertia::render('Dashboard', [
            'clients' => $clients,
            'accountName' => $account,
            'widgets' => $widgets,
            'announcements'=> $announcements
        ]);
    }
}
