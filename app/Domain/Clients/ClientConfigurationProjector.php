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
use App\Models\GatewayProviders\GatewayProvider;
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
        /** @var Client $client */
        $client = Client::find($event->aggregateRootUuid());
        $payload = $event->payload;
        $known_gateways = [];
        $settings = [
            ClientGatewaySetting::NAME_MAILGUN_DOMAIN => GatewayProvider::PROVIDER_SLUG_MAILGUN,
            ClientGatewaySetting::NAME_MAILGUN_SECRET => GatewayProvider::PROVIDER_SLUG_MAILGUN,
            ClientGatewaySetting::NAME_MAILGUN_FROM_ADDRESS => GatewayProvider::PROVIDER_SLUG_MAILGUN,
            ClientGatewaySetting::NAME_MAILGUN_FROM_NAME => GatewayProvider::PROVIDER_SLUG_MAILGUN,
            ClientGatewaySetting::NAME_TWILIO_SID => GatewayProvider::PROVIDER_SLUG_TWILIO_SMS,
            ClientGatewaySetting::NAME_TWILIO_TOKEN => GatewayProvider::PROVIDER_SLUG_TWILIO_SMS,
            ClientGatewaySetting::NAME_TWILIO_NUMBER => GatewayProvider::PROVIDER_SLUG_TWILIO_SMS,
            ClientGatewaySetting::NAME_TWILIO_CONVERSATION_SERVICES_ID => GatewayProvider::PROVIDER_SLUG_TWILIO_CONVERSION,
            ClientGatewaySetting::NAME_TWILIO_MESSENGER_ID => GatewayProvider::PROVIDER_SLUG_TWILIO_CONVERSION,
        ];

        foreach ($settings as $name => $provider) {
            if (! isset($payload[$name])) {
                continue;
            }

            if (! isset($known_gateways[$provider])) {
                $known_gateways[$provider] = $client->getGatewayProviderBySlug($provider);
            }

            $gateway = ClientGatewaySetting::whereName($name)->first() ?: new ClientGatewaySetting();
            $gateway->fill([
                'gateway_provider' => $known_gateways[$provider]->id,
                'name' => $name,
                'value' => $payload[$name],
                'client_id' => $client->id,
            ]);
            $gateway->writeable()->save();
        }
    }
}
