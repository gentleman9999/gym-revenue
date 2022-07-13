<?php

namespace App\Http\Controllers\Comm;

use App\Domain\Audiences\Audience;
use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class DripCampaignsController extends Controller
{
    public function index()
    {
        $page_count = 10;

        $dripCampaigns = DripCampaign::filter(request()->only('search', 'trashed'))
            ->sort()
            ->paginate($page_count)
            ->appends(request()->except('page'));

        return Inertia::render('Comms/DripCampaigns/List', [
            'filters' => request()->all('search', 'trashed'),
            'dripCampaigns' => $dripCampaigns,
        ]);
    }

    public function create()
    {
        return Inertia::render('Comms/DripCampaigns/CreateDripCampaign', [
            'audiences' => Audience::get(),
        ]);
    }

    public function edit(DripCampaign $dripCampaign)
    {

//        if (strtotime($dripCampaign->start_at) <= strtotime('now')) {
//            Alert::error("{$dripCampaign->name} cannot be edited since it has already launched.")->flash();
//
//            return Redirect::route('comms.drip-campaigns');
//        }

        return Inertia::render('Comms/DripCampaigns/EditDripCampaign', [
            'dripCampaign' => $dripCampaign,
            'audiences' => Audience::get(),
        ]);
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export()
    {
        return DripCampaign::filter(request()->only('search', 'trashed'))->get();
    }
}
