<?php

namespace App\Domain\Campaigns\ScheduledCampaigns\Actions;

use App\Domain\Audiences\Audience;
use App\Domain\CalendarEvents\Actions\CreateCalendarEvent;
use App\Domain\CalendarEventTypes\CalendarEventType;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaignAggregate;
use App\Domain\Clients\Projections\Client;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Domain\Users\Models\User;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateScheduledCampaign
{
    use AsAction;

    public string $commandSignature = 'scheduled-campaign:create';
    public string $commandDescription = 'Creates a ScheduledCampaign with the given name.';

    public function handle(array $payload, User $user): ScheduledCampaign
    {
        $id = Uuid::new();

        $owner_id = $user->id;
        $location_id = $user->current_location_id;

        ScheduledCampaignAggregate::retrieve($id)->create($payload)->persist();
        if ($payload['call_template_id']) {
            $audience = Audience::find($payload['audience_id']);
            $endUsers = new $audience['entity']();
            $leads = false;
            if ($endUsers instanceof Lead) {
                $people = $audience->getCallable();
                $leads = true;
            } elseif ($endUsers instanceof Member) {
                $people = $audience->getCallable();
            }
            $event_type_id = CalendarEventType::whereClientId($payload['client_id'])
                ->where('type', '=', 'Task')->first()->id;
            foreach ($people as $person) {
                $task = [
                    'title' => 'Call Script Task for ' . $payload['name'] . ' Scheduled Campaign',
                    'client_id' => $payload['client_id'],
                    'description' => 'Use the Call script Template for ' . $payload['name'] . ' Scheduled Campaign and call the audience assigned',
                    'full_day_event' => false,
                    'start' => $payload['send_at'],
                    'end' => $payload['send_at'],
                    'event_type_id' => $event_type_id,
                    'owner_id' => $owner_id,
                    'user_attendees' => request()->user,
                    'lead_attendees' => ($leads ? [$person->id] : []),
                    'member_attendees' => ($leads ? [] : [$person->id]),
                    'location_id' => $location_id,
                    'editable' => false,
                    'call_task' => true,
                ];
                CreateCalendarEvent::run($task);
            }
        }

        return ScheduledCampaign::findOrFail($id);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'max:50'],
            'audience_id' => ['required', 'exists:audiences,id'],
            'send_at' => ['required', 'after:now'],
            'email_template_id' => ['sometimes', 'string'],
            'sms_template_id' => ['sometimes', 'string'],
            'call_template_id' => ['sometimes', 'string'],
            'client_id' => ['required', 'exists:clients,id'],
//            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('scheduled-campaigns.create', ScheduledCampaign::class);
    }

    public function asController(ActionRequest $request): ScheduledCampaign
    {
        return $this->handle(
            $request->validated(),
            $request->user(),
        );
    }

    public function htmlResponse(ScheduledCampaign $scheduledCampaign): RedirectResponse
    {
        Alert::success("Scheduled Campaign '{$scheduledCampaign->name}' was created")->flash();

        return Redirect::route('mass-comms.scheduled-campaigns.edit', $scheduledCampaign->id);
    }

    public function asCommand(Command $command): void
    {
        $client_id = $this->getClient($command);
        $name = $command->ask("Enter ScheduledCampaign Name");

        $template_type_alias = $command->choice("Template Type?", ['Email', 'SMS']);

        if ($template_type_alias === 'Email') {
            $template_type = EmailTemplate::class;
        } elseif ($template_type_alias === 'SMS') {
            $template_type = SmsTemplate::class;
        }
        $template_id = $this->getTemplate($command, $client_id, $template_type);
        $audience_id = $this->getAudience($command, $client_id);

        $send_at = CarbonImmutable::now();

        $payload = compact('name', 'client_id', 'audience_id', 'template_type', 'template_id', 'send_at');
        $user = new User();
        $user->id = $payload['owner_id'];
        $user->current_location_id = $payload['location_id'];

        $scheduledCampaign = $this->handle($payload, $user);

        $command->info('Created ScheduledCampaign ' . $scheduledCampaign->name);
    }

    private function getClient(Command $command): ?string
    {
        $clients = [];
        $client_ids = [];
        $db_clients = Client::whereActive(1)->get();

        foreach ($db_clients as $idx => $client) {
            $clients[$idx + 1] = $client->name;
            $client_ids[$idx + 1] = $client->id;
        }

        $command->info('Select a client');
        foreach ($clients as $idx => $name) {
            $command->warn("[{$idx}] {$name}");
        }
        $client_choice = $command->ask("Select a client");

        if ($client_choice <= count($db_clients)) {
            $client_id = $client_ids[$client_choice];
            $command->info($clients[$client_choice]);
        }

        return $client_id;
    }

    private function getAudience(Command $command, string $client_id): ?string
    {
        $audiences = [];
        $audience_ids = [];
        $db_audiences = Audience::whereClientId($client_id)->get();

        foreach ($db_audiences as $idx => $audience) {
            $audiences[$idx + 1] = $audience->name;
            $audience_ids[$idx + 1] = $audience->id;
        }

        $command->info('Select an Audience');
        foreach ($audiences as $idx => $name) {
            $command->warn("[{$idx}] {$name}");
        }
        $audience_choice = $command->ask("Select an Audience");

        if ($audience_choice <= count($db_audiences)) {
            $audience_id = $audience_ids[$audience_choice];
            $command->info($audiences[$audience_choice]);
        }

        return $audience_id;
    }

    private function getTemplate(Command $command, string $client_id, string $template_model): ?string
    {
        $templates = [];
        $template_ids = [];
        $db_templates = $template_model::whereClientId($client_id)->get();

        foreach ($db_templates as $idx => $template) {
            $templates[$idx + 1] = $template->name;
            $template_ids[$idx + 1] = $template->id;
        }

        $command->info('Select anTemplate');
        foreach ($templates as $idx => $name) {
            $command->warn("[{$idx}] {$name}");
        }
        $template_choice = $command->ask("Select a Template");

        if ($template_choice <= count($db_templates)) {
            $template_id = $template_ids[$template_choice];
            $command->info($templates[$template_choice]);
        }

        return $template_id;
    }
}
