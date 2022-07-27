<?php

namespace App\Domain\Audiences\Actions;

use App\Domain\Audiences\Audience;
use App\Domain\Audiences\AudienceAggregate;
use App\Domain\Clients\Models\Client;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Support\Uuid;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateAudience
{
    use AsAction;

    public string $commandSignature = 'audience:create';
    public string $commandDescription = 'Creates a Audience with the given name.';

    public function handle(array $payload): Audience
    {
        $id = Uuid::new();

        $aggy = AudienceAggregate::retrieve($id);

        $aggy->create($payload)->persist();

        return Audience::findOrFail($id);
    }

    public function asCommand(Command $command): void
    {
        $client_id = $this->getClient($command);
        $name = $command->ask("Enter Audience Name");
        $entity_alias = $command->choice("Audience Entity", ['Lead', 'Member']);

        if ($entity_alias === 'Lead') {
            $entity = Lead::class;
        } elseif ($entity_alias === 'Member') {
            $entity = Member::class;
        }

        $payload = compact('name', 'entity', 'client_id');

        $audience = $this->handle($payload);

        $command->info('Created Audience ' . $audience->name);
    }

    private function getClient($command): ?string
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
}
