<?php

declare(strict_types=1);

namespace App\Domain\Audiences\Actions;

use App\Actions\GymRevAction;
use App\Domain\Audiences\Audience;
use App\Domain\Audiences\AudienceAggregate;
use App\Domain\Clients\Projections\Client;
use App\Support\Uuid;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;

//use App\Domain\Campaigns\DripCampaigns\DripCampaign;

class CreateAudience extends GymRevAction
{
    public string $commandSignature   = 'audience:create';
    public string $commandDescription = 'Creates a Audience with the given name.';

    /**
     * @param array<string, mixed> $data
     *
     */
    public function handle(array $data): Audience
    {
        $id = Uuid::get();

        AudienceAggregate::retrieve($id)->create($data)->persist();

        return Audience::findOrFail($id);
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'entity' => ['required', 'string'],
            'filters' => ['required', 'array', 'min:1'],
            'client_id' => ['string', 'required'],
        ];
    }

    /**
     * @param array<string, mixed> $args
     *
     * @return array
     */
    public function mapArgsToHandle(array $args): array
    {
        return [$args['input']];
    }

    public function prepareForValidation(ActionRequest $request): void
    {
        $request->merge(['entity' => User::class]);
    }

    public function asCommand(Command $command): void
    {
        $audience = $this->handle([
            'client_id' => $this->getClient($command),
            'name' => $command->ask("Enter Audience Name"),
            'entity' => User::class,
        ]);

        $command->info('Created Audience ' . $audience->name);
    }

    public function asController(ActionRequest $request): Audience
    {
        $data = $request->validated();

        return $this->handle(
            $data
        );
    }

    private function getClient($command): ?string
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
}
