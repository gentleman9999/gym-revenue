<?php

namespace App\Domain\Clients;

use App\Domain\Agreements\AgreementCategories\Actions\CreateAgreementCategory;
use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\Audiences\Actions\CreateAudience;
use App\Domain\Clients\Events\ClientCreated;
use App\Domain\Clients\Projections\Client;
use App\Domain\Teams\Actions\CreateTeam;
use App\Domain\Users\Models\Lead;
use App\Domain\Users\Models\Member;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class ClientReactor extends Reactor
{
    public function onClientCreated(ClientCreated $event): void
    {
        $client = Client::findOrFail($event->aggregateRootUuid())->writeable();

        $default_team_name = $client->name . ' Home Office';

        $default_team = CreateTeam::run([
            'client_id' => $client->id,
            'name' => $default_team_name,
            'home_team' => true,
        ]);
        $client->home_team_id = $default_team->id;
        $client->save();

        CreateAudience::run([
            'client_id' => $client->id,
            'name' => 'All Leads',
            'entity' => Lead::class,
            'editable' => false,
        ]);

        CreateAudience::run([
            'client_id' => $client->id,
            'name' => 'All Members',
            'entity' => Member::class,
            'editable' => false,
        ]);

        CreateAgreementCategory::run([
            'client_id' => (string)$client->id,
            'name' => AgreementCategory::NAME_MEMBERSHIP,
        ]);

        CreateAgreementCategory::run([
            'client_id' => $client->id,
            'name' => AgreementCategory::NAME_PERSONAL_TRAINING,
        ]);
    }
}
