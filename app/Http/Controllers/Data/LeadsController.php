<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeadsController extends Controller
{
    public function index(Request $request)
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

        return Inertia::render('Leads/Index', [
            //'locations' => $locations,
            'title' => 'Leads',
            //'isClientUser' => $is_client_user,
            //'filters' => $request->all('search', 'trashed')
        ]);
    }
}
