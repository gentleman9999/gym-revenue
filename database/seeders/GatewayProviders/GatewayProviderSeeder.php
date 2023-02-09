<?php

namespace Database\Seeders\GatewayProviders;

use App\Models\GatewayProviders\GatewayProvider;
use App\Models\GatewayProviders\GatewayProviderType;
use App\Services\GatewayProviders\Profiles\Email\Mailgun;
use App\Services\GatewayProviders\Profiles\SMS\TwilioSMS;
use App\Services\GatewayProviders\Profiles\Voice\TwilioVoice;
use App\Support\Uuid;
use Illuminate\Database\Seeder;

class GatewayProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [];
        foreach (GatewayProviderType::whereActive(1)->get(['id', 'name']) as $record) {
            $types[$record->name] = $record;
        }

        $gateways = [
            'twilio-sms' => [
                'id' => Uuid::get(),
                'name' => 'Twilio SMS',
                'slug' => 'twilio-sms',
                'desc' => 'Send SMS messages through Twilio, starting at $0.03/msg and bulk $0.01/msg',
                'vendor' => 'Twilio, Inc',
                'provider_type' => $types['sms']->id,
                'profile_class' => TwilioSMS::class,
                'provider_rate' => 0.03,
                'provider_bulk_rate' => 0.03,
                'gr_commission_rate' => 0.01,
                'gr_commission_bulk_rate' => 0.01,
                'active' => 1,
            ],
            'twilio-voice' => [
                'id' => Uuid::get(),
                'name' => 'Twilio Voice',
                'slug' => 'twilio-voice',
                'desc' => 'Voice Calls provider by Twilio',
                'vendor' => 'Twilio, Inc',
                'provider_type' => $types['voice']->id,
                'profile_class' => TwilioVoice::class,
                'provider_rate' => 0.03,
                'provider_bulk_rate' => 0.03,
                'gr_commission_rate' => 0.01,
                'gr_commission_bulk_rate' => 0.01,
                'active' => 1,
            ],
            'mailgun' => [
                'id' => Uuid::get(),
                'name' => 'Mailgun',
                'slug' => 'mailgun',
                'desc' => 'Send Emails through Mailgun, starting at $0.02/email and bulk $0.01/email',
                'vendor' => 'MailGun, Inc',
                'provider_type' => $types['email']->id,
                'profile_class' => Mailgun::class,
                'provider_rate' => 0.02,
                'provider_bulk_rate' => 0.01,
                'gr_commission_rate' => 0.01,
                'gr_commission_bulk_rate' => 0.01,
                'active' => 1,
            ],
            'twilio-conversation' => [
                'id' => Uuid::get(),
                'name' => 'Twilio Conversation',
                'slug' => 'twilio-conversation',
                'desc' => 'Create 1-to-1 or multiparty conversations for customer care and conversational commerce',
                'vendor' => 'Twilio, Inc',
                'provider_type' => $types['chat']->id,
                'profile_class' => TwilioVoice::class,
                'provider_rate' => 0.03,
                'provider_bulk_rate' => 0.03,
                'gr_commission_rate' => 0.01,
                'gr_commission_bulk_rate' => 0.01,
                'active' => 1,
            ],
        ];

        GatewayProvider::upsert($gateways, ['slug']);
    }
}
