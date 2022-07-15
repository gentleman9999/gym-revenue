<?php

namespace App\Domain\Clients;

use App\Domain\Clients\Enums\SocialMediaEnum;
use App\Domain\Clients\Events\ClientCreated;
use App\Domain\Clients\Events\ClientDeleted;
use App\Domain\Clients\Events\ClientGatewaySet;
use App\Domain\Clients\Events\ClientLogoDeleted;
use App\Domain\Clients\Events\ClientLogoUploaded;
use App\Domain\Clients\Events\ClientRestored;
use App\Domain\Clients\Events\ClientServicesSet;
use App\Domain\Clients\Events\ClientSocialMediaSet;
use App\Domain\Clients\Events\ClientTrashed;
use App\Domain\Clients\Events\ClientUpdated;
use App\Domain\Clients\Models\Client;
use App\Domain\Clients\Models\ClientGatewaySetting;
use App\Domain\Clients\Models\ClientSocialMedia;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
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
        $data = $event->payload;

        $logoToDelete = File::whereClientId($data['client_id'])
            ->whereType('logo')
            ->first();

        if (! is_null($logoToDelete)) {
            Storage::disk('s3')->delete($logoToDelete->key);
            $logoToDelete->forceDelete();
        }

        $file_table_data = array_filter($data, function ($key) {
            return in_array($key, (new File())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $file_table_data['hidden'] = true;
        $file_table_data['type'] = 'logo';
        $file = File::create($file_table_data);
        //TODO: consider moving this to reactor?
        $file->url = Storage::disk('s3')->url($file->key);
        $file->save();
    }

    public function onLogoDeleted(ClientLogoDeleted $event)
    {
        $logo = File::whereClientId($event->aggregateRootUuid())->whereType('logo')->first();

        $logo->forceDelete();
    }

    public function onClientSocialMediasSet(ClientSocialMediaSet $event)
    {
        $payload = $event->payload;
        foreach (SocialMediaEnum::cases() as $socialMediaEnum) {
            if (array_key_exists($socialMediaEnum->name, $payload)) {
                $social = ClientSocialMedia::whereName($socialMediaEnum->name)->first();
                if (is_null($social)) {
                    $social = (new ClientSocialMedia())->writeable();
                    $social->client_id = $event->aggregateRootUuid();
                    $social->fill([
                        'name' => $socialMediaEnum->name,
                        'value' => $payload[$socialMediaEnum->name],
                    ]);
                    $social->save();
                } else {
                    $social->writeable()->updateOrFail([
                        'value' => $payload[$socialMediaEnum->name],
                    ]);
                }
            }
        }
    }

    public function onClientGatewaySet(ClientGatewaySet $event)
    {
        $payload = $event->payload;
        if (array_key_exists('mailgunDomain', $payload)) {
            $gateway = ClientGatewaySetting::whereName('mailgunDomain')->first();
            if (is_null($gateway)) {
                $gateway = (new ClientGatewaySetting())->writeable();
                $gateway->client_id = $event->aggregateRootUuid();
                $gateway->fill([
                    'gateway_provider' => 'mailgun',
                    'name' => 'mailgunDomain',
                    'value' => $payload['mailgunDomain'],
                ]);
                $gateway->save();
            } else {
                $gateway->writeable()->update([
                    'gateway_provider' => 'mailgun',
                    'name' => 'mailgunDomain',
                    'value' => $payload['mailgunDomain'],
                ]);
            }
        }
        if (array_key_exists('mailgunSecret', $payload)) {
            $gateway = ClientGatewaySetting::whereName('mailgunSecret')->first();
            if (is_null($gateway)) {
                $gateway = (new ClientGatewaySetting())->writeable();
                $gateway->client_id = $event->aggregateRootUuid();
                $gateway->fill([
                    'gateway_provider' => 'mailgun',
                    'name' => 'mailgunSecret',
                    'value' => $payload['mailgunSecret'],
                ]);
                $gateway->save();
            } else {
                $gateway->writeable()->update([
                    'gateway_provider' => 'mailgun',
                    'name' => 'mailgunSecret',
                    'value' => $payload['mailgunSecret'],
                ]);
            }
        }
        if (array_key_exists('mailgunFromAddress', $payload)) {
            $gateway = ClientGatewaySetting::whereName('mailgunFromAddress')->first();
            if (is_null($gateway)) {
                $gateway = (new ClientGatewaySetting())->writeable();
                $gateway->client_id = $event->aggregateRootUuid();
                $gateway->fill([
                    'gateway_provider' => 'mailgun',
                    'name' => 'mailgunFromAddress',
                    'value' => $payload['mailgunFromAddress'],
                ]);
                $gateway->save();
            } else {
                $gateway->writebale()->update([
                    'gateway_provider' => 'mailgun',
                    'name' => 'mailgunFromAddress',
                    'value' => $payload['mailgunFromAddress'],
                ]);
            }
        }
        if (array_key_exists('mailgunFromName', $payload)) {
            $gateway = ClientGatewaySetting::whereName('mailgunFromName')->first();
            if (is_null($gateway)) {
                $gateway = (new ClientGatewaySetting())->writeable();
                $gateway->client_id = $event->aggregateRootUuid();
                $gateway->fill([
                    'gateway_provider' => 'mailgun',
                    'name' => 'mailgunFromName',
                    'value' => $payload['mailgunFromName'],
                ]);
                $gateway->save();
            } else {
                $gateway->writeable()->update([
                    'gateway_provider' => 'mailgun',
                    'name' => 'mailgunFromName',
                    'value' => $payload['mailgunFromName'],
                ]);
            }
        }
        if (array_key_exists('twilioSID', $payload)) {
            $gateway = ClientGatewaySetting::whereName('twilioSID')->first();
            if (is_null($gateway)) {
                $gateway = (new ClientGatewaySetting())->writeable();
                $gateway->client_id = $event->aggregateRootUuid();
                $gateway->fill([
                    'gateway_provider' => 'twilio',
                    'name' => 'twilioSID',
                    'value' => $payload['twilioSID'],
                ]);
                $gateway->save();
            } else {
                $gateway->writeable()->update([
                    'gateway_provider' => 'twilio',
                    'name' => 'twilioSID',
                    'value' => $payload['twilioSID'],
                ]);
            }
        }
        if (array_key_exists('twilioToken', $payload)) {
            $gateway = ClientGatewaySetting::whereName('twilioToken')->first();
            if (is_null($gateway)) {
                $gateway = (new ClientGatewaySetting())->writeable();
                $gateway->client_id = $event->aggregateRootUuid();
                $gateway->fill([
                    'gateway_provider' => 'twilio',
                    'name' => 'twilioToken',
                    'value' => $payload['twilioToken'],
                ]);
                $gateway->save();
            } else {
                $gateway->writeable()->update([
                    'gateway_provider' => 'twilio',
                    'name' => 'twilioToken',
                    'value' => $payload['twilioToken'],
                ]);
            }
        }
        if (array_key_exists('twilioNumber', $payload)) {
            $gateway = ClientGatewaySetting::whereName('twilioNumber')->first();
            if (is_null($gateway)) {
                $gateway = (new ClientGatewaySetting())->writeable();
                $gateway->client_id = $event->aggregateRootUuid();
                $gateway->fill([
                    'gateway_provider' => 'twilio',
                    'name' => 'twilioNumber',
                    'value' => $payload['twilioNumber'],
                ]);
                $gateway->save();
            } else {
                $gateway->writeable()->update([
                    'gateway_provider' => 'twilio',
                    'name' => 'twilioNumber',
                    'value' => $payload['twilioNumber'],
                ]);
            }
        }
    }
}
