<?php

namespace App\Http\Controllers;

use App\Domain\Audiences\Audience;
use App\Domain\Campaigns\DripCampaigns\DripCampaign;
//use App\Domain\Campaigns\ScheduledCampaigns\Actions\CreateScheduledCampaign;
//use App\Domain\Campaigns\ScheduledCampaigns\Actions\UpdateScheduledCampaign;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\Domain\LeadTypes\LeadType;
use App\Domain\Teams\Models\Team;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Models\Endusers\MembershipType;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class MassCommunicationController extends Controller
{
    public function index(string $type = 'scheduled')
    {
        $client_id = request()->user()->client_id;
        if (! $client_id) {
            return Redirect::route('dashboard');
        }

        $user = auth()->user();
        $session_team = session()->get('current_team');
        if ($session_team && array_key_exists('id', $session_team)) {
            $team = Team::find($session_team['id']);
        } else {
            $team = Team::find($user->default_team_id);
        }
        $campaignType = null;
        if ($type === 'scheduled') {
            $campaignType = new ScheduledCampaign();
        } elseif ($type === 'drip') {
            $campaignType = new DripCampaign();
        } else {
            abort(404, "Unknown campaign type: $campaignType");
        }
        $data = $this->getDashData($campaignType);
        $data['teamName'] = $team->name;

        if (! is_null($team->client)) {
            return Inertia::render(
                'MassCommunication/Show',
                $data
            );
        } else {
            abort(403);
        }
    }

    protected function getDashData(DripCampaign | ScheduledCampaign $Model)
    {
        $audience = Audience::all();
        $lead_types = LeadType::all()->unique('name');
        $member_types = MembershipType::all()->unique('name');

        $email_templates = EmailTemplate::with('creator')
            ->filter(request()->only('search', 'trashed'))
            ->sort()->get();

        $sms_templates = SmsTemplate::with('creator')
            ->filter(request()->only('search', 'trashed'))
            ->sort()->get();
        $campaigns = $Model::all();
        $campaigns = $campaigns->toArray();

        return
            [
                'topolApiKey' => env('TOPOL_API_KEY'),
                'plansUrl' => env('APP_URL') . "/api/plans",
                'filters' => request()->all('search', 'trashed'),
                'email_templates' => $email_templates->toArray(),
                'sms_templates' => $sms_templates->toArray(),
                'audiences' => $audience,
                'member_types' => $member_types,
                'lead_types' => $lead_types,
                'campaigns' => $campaigns,
                'type' => (new \ReflectionClass($Model))->getShortName(),
            ];
    }

    public function campaignDash(string $type = "scheduled")
    {
        $client_id = request()->user()->client_id;
        if (! $client_id) {
            return Redirect::route('dashboard');
        }

        $campaignType = null;
        if ($type === 'scheduled') {
            $campaignType = new ScheduledCampaign();
        } elseif ($type === 'drip') {
            $campaignType = new DripCampaign();
        } else {
            abort(404, "Unknown campaign type: $campaignType");
        }
        $data = $this->getDashData($campaignType);

        return Inertia::render(
            "MassCommunication/CampaignDash",
            $data
        );
    }

    public function getDripCampaign(DripCampaign $dripCampaign)
    {
        return $dripCampaign->load('days');
    }

    public function getScheduledCampaign(ScheduledCampaign $scheduledCampaign)
    {
        return $scheduledCampaign;
    }
}
