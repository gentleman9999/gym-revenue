<?php

namespace Database\Seeders\GatewayProviders;

use App\Models\GatewayProviders\GatewayProvider;
use App\Models\GatewayProviders\GatewayProviderType;
use App\Services\GatewayProviders\Profiles\Email\Mailgun;
use App\Services\GatewayProviders\Profiles\SMS\Twilio;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class GatewayProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = GatewayProviderType::getAllTypesAsArray();
        $gateways = [
            'twilio' => [
                'name' => 'Twilio SMS',
                'slug' => 'twilio',
                'desc' => 'Send SMS messages through Twilio, starting at $0.03/msg and bulk $0.01/msg',
                'vendor' => 'Twilio, Inc',
                'provider_type' => $types['sms']['id'],
                'profile_class' => Twilio::class,
                'provider_rate' => 0.03,
                'provider_bulk_rate' => 0.03,
                'gr_commission_rate' => 0.01,
                'gr_commission_bulk_rate' => 0.01,
                'active' => 1
            ],
            'mailgun' => [
                'name' => 'Mailgun',
                'slug' => 'mailgun',
                'desc' => 'Send Emails through Mailgun, starting at $0.02/email and bulk $0.01/email',
                'vendor' => 'MailGun, Inc',
                'provider_type' => $types['email']['id'],
                'profile_class' => Mailgun::class,
                'provider_rate' => 0.02,
                'provider_bulk_rate' => 0.01,
                'gr_commission_rate' => 0.01,
                'gr_commission_bulk_rate' => 0.01,
                'active' => 1
            ],
        ];


        foreach($gateways as $slug => $gateway)
        {
            VarDumper::dump("($slug) - {$gateway['desc']}");
            GatewayProvider::firstOrCreate($gateway);
        }
    }
}
