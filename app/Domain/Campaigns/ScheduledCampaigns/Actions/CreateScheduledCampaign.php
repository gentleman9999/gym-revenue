<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\ScheduledCampaigns\Actions;

use App\Actions\GymRevAction;
use App\Domain\Audiences\Audience;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaignAggregate;
use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Projections\Location;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Support\Uuid;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class CreateScheduledCampaign extends GymRevAction
{
    public string $commandSignature   = 'scheduled-campaign:create';
    public string $commandDescription = 'Creates a ScheduledCampaign with the given name.';

    /**
     * @param array<string, mixed> $payload
     *
     */
    public function handle(array $payload): ScheduledCampaign
    {
        $id = Uuid::get();

        //NEEDED FOR LIVE REPORTING
        $location                 = Location::whereClientId($payload['client_id'])->first();
        $payload['gymrevenue_id'] = $location->gymrevenue_id;

        ScheduledCampaignAggregate::retrieve($id)->create($payload)->persist();

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

    /**
     * @param array<string, mixed> $args
     *
     * @return array
     */
    public function mapArgsToHandle(array $args): array
    {
        return [$args['campaign']];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('scheduled-campaigns.create', ScheduledCampaign::class);
    }

    public function asController(ActionRequest $request): ScheduledCampaign
    {
        return $this->handle(
            $request->validated()
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
        $name      = $command->ask("Enter ScheduledCampaign Name");

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

        $scheduledCampaign = $this->handle($payload);

        $command->info('Created ScheduledCampaign ' . $scheduledCampaign->name);
    }

    private function getClient(Command $command): ?string
    {
        $clients    = [];
        $client_ids = [];
        $db_clients = Client::whereActive(1)->get();

        foreach ($db_clients as $idx => $client) {
            $clients[$idx + 1]    = $client->name;
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
        $audiences    = [];
        $audience_ids = [];
        $db_audiences = Audience::whereClientId($client_id)->get();

        foreach ($db_audiences as $idx => $audience) {
            $audiences[$idx + 1]    = $audience->name;
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
        $templates    = [];
        $template_ids = [];
        $db_templates = $template_model::whereClientId($client_id)->get();

        foreach ($db_templates as $idx => $template) {
            $templates[$idx + 1]    = $template->name;
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
