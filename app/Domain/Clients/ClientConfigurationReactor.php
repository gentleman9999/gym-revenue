<?php

declare(strict_types=1);

namespace App\Domain\Clients;

use App\Domain\Clients\Events\ClientLogoUploaded;
use App\Domain\Clients\Projections\Client;
use App\Domain\Users\Models\User;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class ClientConfigurationReactor extends Reactor
{
    public function onLogoUploaded(ClientLogoUploaded $event): void
    {
        $data  = $event->payload;
        $model = Client::find($data['client_id']);

        \App\Actions\Clients\Files\CreateFile::run($data, $model, User::find($event->userId()));
    }
}
