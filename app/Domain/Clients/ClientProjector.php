<?php

namespace App\Domain\Clients;

use App\Domain\Clients\Events\ClientCreated;
use App\Domain\Clients\Events\ClientDeleted;
use App\Domain\Clients\Events\ClientLogoUploaded;
use App\Domain\Clients\Events\ClientRestored;
use App\Domain\Clients\Events\ClientServicesSet;
use App\Domain\Clients\Events\ClientSocialMediaSet;
use App\Domain\Clients\Events\ClientTrashed;
use App\Domain\Clients\Events\ClientUpdated;
use App\Domain\Clients\Models\Client;
use App\Models\SocialMedia;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientProjector extends Projector
{
    public function onClientCreated(ClientCreated $event): void
    {
        $client = (new Client())->writeable();
        $client->fill($event->payload);
        $client->id = $event->aggregateRootUuid();

        $default_team_name = $client->name . ' Home Office';
        preg_match_all('/(?<=\s|^)[a-z]/i', $default_team_name, $matches);
        $prefix = strtoupper(implode('', $matches[0]));
        $prefix = (strlen($prefix) > 3) ? substr($prefix, 0, 3) : $prefix;
        $client->prefix = $prefix;

        $client->save();
    }

    public function onClientUpdated(ClientUpdated $event)
    {
        Client::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail($event->payload);
    }

    public function onClientTrashed(ClientTrashed $event)
    {
        Client::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onClientRestored(ClientRestored $event)
    {
        Client::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onClientDeleted(ClientDeleted $event): void
    {
        Client::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onClientServicesSet(ClientServicesSet $event)
    {
        $client = Client::findOrFail($event->aggregateRootUuid())->writeable();
        $client->services = $event->services;
        $client->save();
    }

    public function onLogoUploaded(ClientLogoUploaded $event)
    {
    }

    public function onClientSocialMediasSet(ClientSocialMediaSet $event)
    {
        $payload = $event->payload;
        if (array_key_exists('facebook', $payload)) {
            $social = SocialMedia::whereClientId($payload['client_id'])
                ->whereName('facebook')->first();
            if (is_null($social)) {
                SocialMedia::create([
                    'client_id' => $payload['client_id'],
                    'name' => 'facebook',
                    'value' => $payload['facebook'],
                ]);
            } else {
                $social->update([
                    'client_id' => $payload['client_id'],
                    'name' => 'facebook',
                    'value' => $payload['facebook'],
                ]);
            }
        }
        if (array_key_exists('twitter', $payload)) {
            $social = SocialMedia::whereClientId($payload['client_id'])
                ->whereName('twitter')->first();
            if (is_null($social)) {
                SocialMedia::create([
                    'client_id' => $payload['client_id'],
                    'name' => 'twitter',
                    'value' => $payload['twitter'],
                ]);
            } else {
                $social->update([
                    'client_id' => $payload['client_id'],
                    'name' => 'twitter',
                    'value' => $payload['twitter'],
                ]);
            }
        }
        if (array_key_exists('instagram', $payload)) {
            $social = SocialMedia::whereClientId($payload['client_id'])
                ->whereName('instagram')->first();
            if (is_null($social)) {
                SocialMedia::create([
                    'client_id' => $payload['client_id'],
                    'name' => 'instagram',
                    'value' => $payload['instagram'],
                ]);
            } else {
                $social->update([
                    'client_id' => $payload['client_id'],
                    'name' => 'instagram',
                    'value' => $payload['instagram'],
                ]);
            }
        }
        if (array_key_exists('linkedin', $payload)) {
            $social = SocialMedia::whereClientId($payload['client_id'])
                ->whereName('linkedin')->first();
            if (is_null($social)) {
                SocialMedia::create([
                    'client_id' => $payload['client_id'],
                    'name' => 'linkedin',
                    'value' => $payload['linkedin'],
                ]);
            } else {
                $social->update([
                    'client_id' => $payload['client_id'],
                    'name' => 'linkedin',
                    'value' => $payload['linkedin'],
                ]);
            }
        }
    }
}
