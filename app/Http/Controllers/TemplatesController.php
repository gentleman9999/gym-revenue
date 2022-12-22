<?php

namespace App\Http\Controllers;

//use App\Domain\Campaigns\ScheduledCampaigns\Actions\CreateScheduledCampaign;
//use App\Domain\Campaigns\ScheduledCampaigns\Actions\UpdateScheduledCampaign;
use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Models\Endusers\MembershipType;
use App\Support\CurrentInfoRetriever;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TemplatesController extends Controller
{
    public function index(string $type = 'scheduled')
    {
        $client_id = request()->user()->client_id;
        if (! $client_id) {
            return Redirect::route('dashboard');
        }

        $user = auth()->user();
        $team = CurrentInfoRetriever::getCurrentTeam();
        $data = $this->getDashData();
        $data['teamName'] = $team->name;

        if (! is_null($team->client)) {
            return Inertia::render(
                'Templates/Show',
                $data
            );
        } else {
            abort(403);
        }
    }

    protected function getDashData()
    {
        $member_types = MembershipType::all()->unique('name');

        $email_templates = EmailTemplate::with('creator')
            ->filter(request()->only('search', 'trashed'))
            ->sort()->get();

        $sms_templates = SmsTemplate::with('creator')
            ->filter(request()->only('search', 'trashed'))
            ->sort()->get();

        $call_script_templates = CallScriptTemplate::with('creator')
            ->filter(request()->only('search', 'trashed'))
            ->sort()->get();


        return
            [
                'topolApiKey' => env('TOPOL_API_KEY'),
                'plansUrl' => env('APP_URL') . "/api/plans",
                'filters' => request()->all('search', 'trashed'),
                'email_templates' => $email_templates->toArray(),
                'sms_templates' => $sms_templates->toArray(),
                'call_templates' => $call_script_templates->toArray(),
                'member_types' => $member_types,
            ];
    }
}
