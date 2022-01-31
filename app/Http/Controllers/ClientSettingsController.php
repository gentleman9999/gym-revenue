<?php

namespace App\Http\Controllers;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\Client;
use App\Models\Clients\ClientDetail;
use App\Models\Clients\Features\ClientService;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class ClientSettingsController extends Controller
{

    public function index()
    {
        return Inertia::render('ClientSettings/Index', [
            'availableServices' => ClientService::whereClientId(request()->user()->currentClientId())->get(['feature_name', 'slug', 'id']),
            'services' => Client::with('details')->find(request()->user()->currentClientId())->services
        ]);
    }

    public function update()
    {
        $data = request()->validate(['services' => ['sometimes', 'array']]);
        $client_id = request()->user()->currentClientId();
        if (array_key_exists('services', $data) && is_array($data['services'])) {
            ClientAggregate::retrieve($client_id)->setClientServices($data['services'], request()->user()->id)->persist();
        }
        Alert::success("Client Services updated.")->flash();

        return Redirect::back();
    }
}
