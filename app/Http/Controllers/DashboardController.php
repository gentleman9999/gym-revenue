<?php

namespace App\Http\Controllers;

use App\Models\Clients\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected Client $clients;

    public function __construct(Client $clients)
    {
        $this->clients = $clients;
    }

    public function index()
    {
        $team = auth()->user()->currentTeam;
        $client_detail = $team->client_details()->first();

        if(!is_null($client_detail))
        {
            $clients = collect([$client_detail->client->toArray()]);
        }
        else {
            $clients = $this->clients->all();
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
            'clients' => $clients
        ]);
    }
}
