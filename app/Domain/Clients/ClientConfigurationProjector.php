<?php

namespace App\Domain\Clients;

use App\Domain\Clients\Enums\SocialMediaEnum;
use App\Domain\Clients\Events\ClientCommsPrefsSet;
use App\Domain\Clients\Events\ClientGatewaySet;
use App\Domain\Clients\Events\ClientLogoDeleted;
use App\Domain\Clients\Events\ClientLogoUploaded;
use App\Domain\Clients\Events\ClientServicesSet;
use App\Domain\Clients\Events\ClientSocialMediaSet;
use App\Domain\Clients\Models\ClientGatewaySetting;
use App\Domain\Clients\Models\ClientSocialMedia;
use App\Domain\Clients\Projections\Client;
use App\Models\ClientCommunicationPreference;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ClientConfigurationProjector extends Projector
{
    public function onClientServicesSet(ClientServicesSet $event): void
    {
        $client = Client::findOrFail($event->aggregateRootUuid())->writeable();
        $client->services = $event->services;
        $client->save();
    }

    public function onClientCommsPrefsSet(ClientCommsPrefsSet $event): void
    {
        /** @var ClientCommunicationPreference $client */
        $client = ClientCommunicationPreference::whereClientId($event->aggregateRootUuid())->first()?->writeable();
        if ($client) {
            $client->client_id = $event->clientId();
        } else {
            $client = (new ClientCommunicationPreference())->writeable();
            $client->client_id = $event->aggregateRootUuid();
        }

        foreach (array_keys(ClientCommunicationPreference::COMMUNICATION_TYPES) as $type) {
            $client->{$type} = $event->commsPreferences[$type];
        }

        $client->save();
    }

    public function onLogoUploaded(ClientLogoUploaded $event): void
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

    public function onLogoDeleted(ClientLogoDeleted $event): void
    {
        $logo = File::whereClientId($event->aggregateRootUuid())->whereType('logo')->first();

        $logo->forceDelete();
    }

    public function onClientSocialMediasSet(ClientSocialMediaSet $event): void
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

    public function onClientGatewaySet(ClientGatewaySet $event): void
    {
        //TODO: fire off action to set client_gateway_integration
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
                    'gateway_provider' => 'twilio-sms',
                    'name' => 'twilioSID',
                    'value' => $payload['twilioSID'],
                ]);
                $gateway->save();
            } else {
                $gateway->writeable()->update([
                    'gateway_provider' => 'twilio-sms',
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
                    'gateway_provider' => 'twilio-sms',
                    'name' => 'twilioToken',
                    'value' => $payload['twilioToken'],
                ]);
                $gateway->save();
            } else {
                $gateway->writeable()->update([
                    'gateway_provider' => 'twilio-sms',
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
                    'gateway_provider' => 'twilio-sms',
                    'name' => 'twilioNumber',
                    'value' => $payload['twilioNumber'],
                ]);
                $gateway->save();
            } else {
                $gateway->writeable()->update([
                    'gateway_provider' => 'twilio-sms',
                    'name' => 'twilioNumber',
                    'value' => $payload['twilioNumber'],
                ]);
            }
        }
    }
}
