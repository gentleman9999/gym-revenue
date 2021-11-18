<?php

namespace App\Http\Controllers\Comm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MassCommunicationsController extends Controller
{
    public function index()
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

        return Inertia::render('Comms/MassCommsDashboard', [

        ]);
    }
}
