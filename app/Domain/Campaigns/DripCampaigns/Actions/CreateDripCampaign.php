<?php

namespace App\Domain\Campaigns\DripCampaigns\Actions;

use App\Domain\Audiences\Audience;
use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\Domain\Campaigns\DripCampaigns\DripCampaignAggregate;
use App\Domain\Clients\Projections\Client;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateDripCampaign
{
    use AsAction;

    public string $commandSignature = 'drip-campaign:create';
    public string $commandDescription = 'Creates a DripCampaign with the given name.';

    public function handle(array $payload): DripCampaign
    {
        $id = Uuid::new();

        $aggy = DripCampaignAggregate::retrieve($id);

        $aggy->create($payload)->persist();

        $dripCampaign = DripCampaign::findOrFail($id);
        foreach ($payload['days'] as $day) {
            $insertDay['drip_campaign_id'] = $dripCampaign->id;
            $insertDay['day_of_campaign'] = $day['day_in_campaign'];
            $insertDay['email_template_id'] = $day['email'];
            $insertDay['sms_template_id'] = $day['sms'];
            $insertDay['call_template_id'] = $day['call'];
            CreateDripCampaignDay::run($insertDay);
        }

        return $dripCampaign;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'name' => ['required', 'max:50'],
            'audience_id' => ['required', 'exists:audiences,id'],
            'start_at' => ['sometimes', 'nullable', 'after:now'],
            'end_at' => ['sometimes', 'nullable', 'after:start_at'],
            'completed_at' => ['sometimes', 'nullable', 'after:start_at'],
            'days' => ['array', 'min:1'],
            'status' => ['required'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('drip-campaigns.create', DripCampaign::class);
    }

    public function asController(ActionRequest $request): DripCampaign
    {
        return $this->handle(
            $request->validated()
        );
    }

    public function htmlResponse(DripCampaign $dripCampaign): RedirectResponse
    {
        Alert::success("Drip Campaign '{$dripCampaign->name}' was created")->flash();

        return Redirect::route('mass-comms.drip-campaigns.edit', $dripCampaign->id);
    }

    public function asCommand(Command $command): void
    {
        $client_id = $this->getClient($command);
        $name = $command->ask("Enter DripCampaign Name");

        $audience_id = $this->getAudience($command, $client_id);

        $start_at = CarbonImmutable::now();

        $payload = compact('name', 'client_id', 'audience_id', 'start_at');
        $dripCampaign = $this->handle($payload);

        $command->info('Created DripCampaign ' . $dripCampaign->name);
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
}
